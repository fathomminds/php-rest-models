<?php
namespace Fathomminds\Rest\Contracts;

interface IDatabase
{
    public function get($resourceName, $resourceId = null);
    public function post($resourceName, $newResource);
    public function put($resourceName, $resourceId, $newResource);
    public function delete($resourceName, $resourceId);
    public function getCollection($resourceName);
}
