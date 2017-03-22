<?php
namespace Fathomminds\Rest\Contracts;

interface IRestModel
{
    public function createFromObject(\StdClass $obj);
    public function getResource();
    public function one($resourceId);
    public function all();
    public function create();
    public function update();
    public function delete();
    public function validate();
    public function toArray();
    public function getProperty($propertyName);
    public function setProperty($propertyName, $propertyValue);
}
