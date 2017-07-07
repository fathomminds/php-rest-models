<?php
namespace Fathomminds\Rest\Objects;

use Fathomminds\Rest\Helpers\ReflectionHelper;
use Fathomminds\Rest\Contracts\IRestObject;
use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Database\Clusterpoint\Database;

abstract class RestObject implements IRestObject
{
    protected $resourceName;
    protected $primaryKey;
    protected $resource;
    protected $schemaClass;
    protected $schema;
    protected $databaseClass;
    protected $database;
    protected $indexNames = [];
    protected $allowExtraneous = false;
    public $updateMode = false;

    public function __construct($resource = null, $schema = null, $database = null)
    {
        $reflectionHelper = new ReflectionHelper;
        $this->resource = $resource === null ? $reflectionHelper->createInstance($this->schemaClass) : $resource;
        $this->database = $database === null ? $reflectionHelper->createInstance($this->databaseClass) : $database;
        $this->schema = $schema === null ? new SchemaValidator : $schema;
        $this->schema->allowExtraneous($this->allowExtraneous);
    }

    public function createFromObject($object)
    {
        $reflectionHelper = new ReflectionHelper;
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$object]);
        return $this;
    }

    public function setDatabaseName($databaseName)
    {
        $this->database->setDatabaseName($databaseName);
    }

    public function getDatabaseName()
    {
        return $this->database->getDatabaseName();
    }

    public function resource()
    {
        return $this->resource;
    }

    public function getResourceName()
    {
        return $this->resourceName;
    }

    public function get($resourceId = null)
    {
        $reflectionHelper = new ReflectionHelper;
        if ($resourceId === null) {
            $rawResources = $this->database->get($this->resourceName, $this->primaryKey);
            $resources = [];
            foreach ($rawResources as $rawResource) {
                $resources[] = $reflectionHelper->createInstance($this->schemaClass, [$rawResource]);
            }
            return $resources;
        }
        $res = $this->database->get($this->resourceName, $this->primaryKey, $resourceId);
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$res]);
        return $this->resource;
    }

    public function post($newResource)
    {
        $reflectionHelper = new ReflectionHelper;
        $res = $this->database->post($this->resourceName, $this->primaryKey, $newResource);
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$res]);
    }

    public function put($resourceId, $newResource)
    {
        $reflectionHelper = new ReflectionHelper;
        $res = $this->database->put(
            $this->resourceName,
            $this->primaryKey,
            $resourceId,
            $newResource
        );
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$res]);
    }

    public function delete($resourceId)
    {
        $this->database->delete($this->resourceName, $this->primaryKey, $resourceId);
        $this->reset();
    }

    public function reset()
    {
        $reflectionHelper = new ReflectionHelper;
        $this->resource = $reflectionHelper->createInstance($this->schemaClass);
    }

    protected function setUpdateMode($value)
    {
        $this->updateMode = $value;
    }

    public function setFieldDefaults()
    {
        $properties = get_object_vars($this->resource);
        foreach ($this->schema->getFields($this->resource) as $fieldName => $field) {
            if (isset($properties[$fieldName])) {
                continue;
            }
            if (!array_key_exists('default', $field)) {
                continue;
            }
            $this->setFieldDefaultValue($fieldName, $field['default']);
        }
    }

    protected function setFieldDefaultValue($fieldName, $value)
    {
        if (gettype($value) === 'object' && is_callable($value)) {
            $this->resource->{$fieldName} = $value();
            return;
        }
        $this->resource->{$fieldName} = $value;
    }

    public function validateSchema($resource)
    {
        $this->schema->validate($resource);
    }

    public function validate()
    {
        $uniqueFields = $this->getUniqueFields();
        if (!empty($uniqueFields)) {
            $this->validateUniqueFields();
        }
    }

    public function toArray()
    {
        return json_decode(json_encode($this->resource), true);
    }

    public function getPrimaryKeyValue()
    {
        if (property_exists($this->resource, $this->primaryKey)) {
            return $this->resource->{$this->primaryKey};
        }
        return null;
    }

    public function getUniqueFields()
    {
        return $this->schema->getUniqueFields($this->resource);
    }

    abstract public function validateUniqueFields();

    protected function getClient()
    {
        return $this->database->getClient();
    }

    abstract public function query();
}
