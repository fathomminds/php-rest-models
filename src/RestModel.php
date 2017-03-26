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

    public function use($obj)
    {
        $this->restObject = $this->restObject->createFromObject($obj);
        return $this;
    }

    public function resource()
    {
        return $this->restObject->resource();
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
        $this->restObject->post($this->resource());
        return $this;
    }

    public function update()
    {
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

    public function validateUniqueFields()
    {
        return $this->restObject->validateUniqueFields();
    }
}
