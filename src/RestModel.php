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

    public function __construct($restObject)
    {
        $this->restObject = $restObject;
    }

    public function createFromObject(\StdClass $obj)
    {
        $this->restObject = $this->restObject->createFromObject($obj);
        return $this;
    }

    public function getResource()
    {
        return $this->restObject->getResource();
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

    public function save()
    {
        return $this->restObject->isNew() ? $this->create() : $this->update();
    }

    public function delete()
    {
        $resourceId = $this->restObject->getPrimaryKeyValue();
        $this->restObject->delete($resourceId);
        return $resourceId;
    }

    public function validate()
    {
        $this->restObject->validate();
    }

    public function getProperty($propertyName)
    {
        return $this->restObject->getProperty($propertyName);
    }

    public function setProperty($propertyName, $propertyValue)
    {
        $this->restObject->setProperty($propertyName, $propertyValue);
        return $this;
    }

    public function toArray()
    {
        return $this->restObject->toArray();
    }

    private function create()
    {
        $this->restObject->post($this->getResource());
        return $this;
    }

    private function update()
    {
        $this->restObject->put($this->restObject->getPrimaryKeyValue(), $this->getResource());
        return $this;
    }

    public function validateUniqueFields()
    {
        return $this->restObject->validateUniqueFields();
    }
}
