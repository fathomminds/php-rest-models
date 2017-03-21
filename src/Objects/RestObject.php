<?php
namespace Fathomminds\Rest\Objects;

use Fathomminds\Rest\Helpers\ReflectionHelper;
use Fathomminds\Rest\Contracts\IRestObject;
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

    public function __construct($resource = null, $schema = null, $database = null)
    {
        $reflectionHelper = new ReflectionHelper;
        $this->resource = $resource === null ? new \StdClass : $resource;
        $this->database = $database === null ? $reflectionHelper->createInstance($this->databaseClass) : $database;
        $this->schema = $schema === null ? $reflectionHelper->createInstance($this->schemaClass) : $schema;
    }

    public function createFromObject(\StdClass $object)
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
            $resources = $this->database->get($this->resourceName, $this->primaryKey);
            return $resources;
        }
        $this->resource = $this->database->get($this->resourceName, $this->primaryKey, $resourceId);
        return $this->resource;
    }

    public function post($newResource)
    {
        $this->schema->validate($newResource);
        $this->resource = $this->database->post($this->resourceName, $this->primaryKey, $newResource);
    }

    public function put($resourceId, $newResource)
    {
        $this->schema->validate($newResource);
        $this->resource = $this->database->put(
            $this->resourceName,
            $this->primaryKey,
            $resourceId,
            $newResource
        );
    }

    public function delete($resourceId)
    {
        $this->database->delete($this->resourceName, $this->primaryKey, $resourceId);
        $this->reset();
    }

    public function reset()
    {
        $this->resource = new \StdClass();
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

    public function getUniqueFields()
    {
        return $this->schema->getUniqueFields();
    }

    abstract public function validateUniqueFields();

    protected function getDatabaseName()
    {
        return $this->database->getDatabaseName();
    }

    protected function getClient()
    {
        return $this->database->getClient();
    }
}
