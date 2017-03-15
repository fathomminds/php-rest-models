<?php
namespace Fathomminds\Clurexid\Rest\Contracts;

use \StdClass;

interface IRestObject
{
    public function createFromObject(StdClass $obj);
    public function getResource();
    public function get($resourceId = null);
    public function post($newResource);
    public function put($resourceId, $newResource);
    public function delete($resourceId);
    public function reset();
    public function validate();
    public function toArray();
    public function getProperty($propertyName);
    public function setProperty($propertyName, $propertyValue);
    public function isNew();
    public function getPrimaryKeyValue();
}
