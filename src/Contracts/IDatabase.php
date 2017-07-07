<?php
namespace Fathomminds\Rest\Contracts;

interface IDatabase
{
    public function get($resourceName, $primaryKey, $resourceId = null);
    public function post($resourceName, $primaryKey, $newResource);
    public function put($resourceName, $primaryKey, $resourceId, $newResource);
    public function delete($resourceName, $primaryKey, $resourceId);
    public function setDatabaseName($databaseName);
    public function getDatabaseName();
    public function getClient();
}
