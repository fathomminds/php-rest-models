<?php
namespace Fathomminds\Clurexid\Rest\Contracts;

use \StdClass;

interface IRestModel
{
    public function createFromObject(StdClass $obj);
    public function getResource();
    public function one($resourceId);
    public function all();
    public function save();
    public function delete();
    public function validate();
    public function toArray();
    public function getProperty($propertyName);
    public function setProperty($propertyName, $propertyValue);
}
