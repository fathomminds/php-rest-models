<?php
namespace Fathomminds\Rest;

use Fathomminds\Rest\Helpers\ReflectionHelper;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Objects\RestObject;
use Fathomminds\Rest\Contracts\IRestModel;

abstract class RestModel implements IRestModel
{
    protected $restObjectClass;
    protected $restObject;

    public function __construct($restObject = null)
    {
        if ($restObject === null) {
            $reflectionHelper = new ReflectionHelper;
            $restObject = $reflectionHelper->createInstance($this->restObjectClass, []);
        }
        $this->restObject = $restObject;
    }

    protected function useResource($obj)
    {
        try {
            $this->restObject = $this->restObject->createFromObject($obj);
        } catch (\Exception $ex) {
            throw new RestException(
                'Setting model resource failed',
                ['originalException' => $ex]
            );
        }
        return $this;
    }

    public function resource($resource = null)
    {
        if ($resource !== null) {
            $this->useResource($resource);
        }
        return $this->restObject->resource();
    }

    public function query()
    {
        return $this->restObject->query();
    }

    public function find($client = null)
    {
        return $this->restObject->find($client);
    }

    public function one($resourceId)
    {
        $this->restObject->get($resourceId);
        if ($this->restObject->getPrimaryKeyValue() !== $resourceId) {
            throw new RestException(
                'Resource does not exist',
                [
                    'resourceName' => $this->restObject->getResourceName(),
                    'resourceId' => $resourceId,
                ]
            );
        }
        return $this;
    }

    public function all()
    {
        $list = $this->restObject->get();
        return $list;
    }

    public function create()
    {
        $this->restObject->updateMode(false);
        $this->restObject->replaceMode(false);
        $this->restObject->setFieldDefaults();
        $this->validate();
        $this->restObject->post($this->resource());
        return $this;
    }

    public function update()
    {
        $this->restObject->updateMode(true);
        $this->restObject->replaceMode(false);
        $this->validate();
        $this->restObject->patch($this->restObject->getPrimaryKeyValue(), $this->resource());
        return $this;
    }

    public function replace()
    {
        $this->restObject->updateMode(false);
        $this->restObject->replaceMode(true);
        $this->restObject->setFieldDefaults();
        $this->validate();
        $this->restObject->put($this->restObject->getPrimaryKeyValue(), $this->resource());
        return $this;
    }

    public function delete()
    {
        $resourceId = $this->restObject->getPrimaryKeyValue();
        $this->restObject->delete($resourceId);
        return $resourceId;
    }

    public function validate()
    {
        $this->restObject->validateSchema($this->resource());
        $this->restObject->validate();
    }

    public function toArray()
    {
        return $this->restObject->toArray();
    }

    public function setDatabaseName($databaseName)
    {
        $this->restObject->setDatabaseName($databaseName);
    }

    public function getDatabaseName()
    {
        return $this->restObject->getDatabaseName();
    }
}
