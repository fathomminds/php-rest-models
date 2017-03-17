<?php
namespace Fathomminds\Clurexid\Rest\Objects;

use \StdClass;
use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;
use Fathomminds\Clurexid\Rest\Helpers\ReflectionHelper;
use Fathomminds\Clurexid\Rest\Contracts\IRestObject;
use Fathomminds\Clurexid\Rest\Database\Clusterpoint\Database;

abstract class RestObject implements IRestObject
{
    protected $resourceName;
    protected $primaryKey;
    protected $resource;
    protected $schemaClass;
    protected $schema;
    protected $databaseClass;
    protected $database;

    public function __construct($resource = null, $schema = null, $database = null)
    {
        $reflectionHelper = new ReflectionHelper;
        $this->resource = $resource === null ? new StdClass : $resource;
        $this->database = $database === null ? $reflectionHelper->createInstance($this->databaseClass) : $database;
        $this->schema = $schema === null ? $reflectionHelper->createInstance($this->schemaClass) : $schema;
    }

    public function createFromObject(StdClass $object)
    {
        $this->resource = $object;
        return $this;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function get($resourceId = null)
    {
        if ($resourceId == null) {
            $resources = $this->database->get($this->resourceName);
            return $resources;
        }
        $this->resource = $this->database->get($this->resourceName, $resourceId);
        return $this->resource;
    }

    public function post($newResource)
    {
        $this->schema->validate($newResource);
        $this->resource = $this->database->post($this->resourceName, $newResource);
    }

    public function put($resourceId, $newResource)
    {
        $this->schema->validate($newResource);
        $this->resource = $this->database->put(
            $this->resourceName,
            $resourceId,
            $newResource
        );
    }

    public function delete($resourceId)
    {
        $this->database->delete($this->resourceName, $resourceId);
        $this->reset();
    }

    public function reset()
    {
        $this->resource = new StdClass();
    }

    public function validate()
    {
        $this->schema->validate($this->resource);
    }

    public function toArray()
    {
        return json_decode(json_encode($this->resource), true);
    }

    public function getProperty($propertyName)
    {
        if (property_exists($this->resource, $propertyName)) {
            return $this->resource->{$propertyName};
        }
        return null;
    }

    public function setProperty($propertyName, $propertyValue)
    {
        $this->resource->{$propertyName} = $propertyValue;
        return $this;
    }

    public function isNew()
    {
        return !property_exists($this->resource, $this->primaryKey) || $this->resource->{$this->primaryKey} === null;
    }

    public function getPrimaryKeyValue()
    {
        if (property_exists($this->resource, $this->primaryKey)) {
            return $this->resource->{$this->primaryKey};
        }
        return null;
    }

    private function getCollection()
    {
        return $this->database->getCollection($this->resourceName);
    }

    public function validateUniqueFields()
    {
        $uniqueFields = $this->schema->getUniqueFields();
        if (empty($uniqueFields)) {
            return;
        }
        $query = null;
        $hasPrimaryKey = false;
        if (property_exists($this->resource, $this->primaryKey)) {
            $hasPrimarKey = true;
            $query = $this->getCollection();
            $query->where($this->primaryKey, '!=', $this->getPrimaryKeyValue());
        }
        $query = $this->getUniqueFieldQuery($query, $uniqueFields);
        $res = $query->limit(1)->get();
        $this->failOnUniqueFieldViolation($res, $uniqueFields);
    }

    private function getUniqueFieldQuery($query = null, $uniqueFields = null)
    {
        $uniqueFields = $uniqueFields === null ? $this->schema->getUniqueFields() : $uniqueFields;
        $query = $query === null ? $this->getCollection() : $query;
        $query->where(function ($query) use ($uniqueFields) {
            foreach ($uniqueFields as $fieldName) {
                if (property_exists($this->resource, $fieldName)) {
                    $query->orWhere($fieldName, '==', $this->resource->{$fieldName});
                }
            }
        });
        return $query;
    }

    private function failOnUniqueFieldViolation($clusterpointResult, $uniqueFields)
    {
        if ($clusterpointResult->hits() === '0') { // Hits returned as string by CP
            return;
        }
        $doc = $clusterpointResult[0]; // limit 1 is applied in validateUniqueFields()
        $confilcts = [];
        foreach ($uniqueFields as $fieldName) {
            if (property_exists($this->resource, $fieldName) &&
                property_exists($doc, $fieldName) &&
                $this->resource->{$fieldName} === $doc->{$fieldName}
            ) {
                $confilcts[] = $fieldName;
            }
            throw new DetailedException(
                'Unique constraint violation',
                [
                    'resourceName' => $this->resourceName,
                    'confilcts' => $confilcts,
                ]
            );
        }
    }
}
