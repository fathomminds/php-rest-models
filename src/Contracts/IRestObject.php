<?php
namespace Fathomminds\Rest\Contracts;

interface IRestObject
{
    public function createFromObject($obj);
    public function resource();
    public function query();
    public function find();
    public function get($resourceId = null);
    public function post($newResource);
    public function put($resourceId, $newResource);
    public function delete($resourceId);
    public function reset();
    public function validate();
    public function toArray();
    public function getPrimaryKeyValue();
}
