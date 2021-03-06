<?php namespace Fathomminds\Rest\Objects;

use Fathomminds\Rest\Helpers\ReflectionHelper;
use Fathomminds\Rest\Contracts\IRestObject;
use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Database\Clusterpoint\Database;

abstract class RestObject implements IRestObject
{
    private $updateMode = false;
    private $replaceMode = false;

    protected $resourceName;
    protected $primaryKey;
    protected $resource;
    protected $schemaClass;
    protected $schema;
    protected $databaseClass;
    protected $database;
    protected $indexNames = [];
    protected $allowExtraneous = false;
    protected $validUniqueFieldTypes = [
        'boolean',
        'integer',
        'double',
        'string',
        'NULL'
    ];

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
            $rawResources = $this->database->get($this->resourceName, $this->schemaClass, $this->primaryKey);
            $resources = [];
            foreach ($rawResources as $rawResource) {
                $resources[] = $reflectionHelper->createInstance($this->schemaClass, [$rawResource]);
            }
            return $resources;
        }
        $res = $this->database->get($this->resourceName, $this->schemaClass, $this->primaryKey, $resourceId);
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$res]);
        return $this->resource;
    }

    public function post($newResource)
    {
        $reflectionHelper = new ReflectionHelper;
        $res = $this->database->post($this->resourceName, $this->schemaClass, $this->primaryKey, $newResource);
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$res]);
    }

    public function patch($resourceId, $newResource)
    {
        $reflectionHelper = new ReflectionHelper;
        $res = $this->database->patch(
            $this->resourceName,
            $this->schemaClass,
            $this->primaryKey,
            $resourceId,
            $newResource
        );
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$res]);
    }

    public function put($resourceId, $newResource)
    {
        $reflectionHelper = new ReflectionHelper;
        $res = $this->database->put(
            $this->resourceName,
            $this->schemaClass,
            $this->primaryKey,
            $resourceId,
            $newResource
        );
        $this->resource = $reflectionHelper->createInstance($this->schemaClass, [$res]);
    }

    public function delete($resourceId)
    {
        $this->database->delete($this->resourceName, $this->schemaClass, $this->primaryKey, $resourceId);
        $this->reset();
    }

    public function reset()
    {
        $reflectionHelper = new ReflectionHelper;
        $this->resource = $reflectionHelper->createInstance($this->schemaClass);
    }

    public function replaceMode($value = null)
    {
        if (is_bool($value)) {
            $this->replaceMode = $value;
        }
        return $this->replaceMode;
    }

    public function updateMode($value = null)
    {
        if (is_bool($value)) {
            $this->updateMode = $value;
        }
        return $this->updateMode;
    }

    public function setFieldDefaults()
    {
        $this->resource->setFieldDefaults();
    }

    public function validateSchema($resource)
    {
        $this->schema->updateMode($this->updateMode());
        $this->schema->replaceMode($this->replaceMode());
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
        return $this->resource->toArray();
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

    abstract public function find();

    abstract public function query();
}
