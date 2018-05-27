<?php
namespace Fathomminds\Rest\Contracts;

interface IDatabase
{
    public function get($resourceName, $schema, $primaryKey, $resourceId = null);
    public function post($resourceName, $schema, $primaryKey, $newResource);
    public function patch($resourceName, $schema, $primaryKey, $resourceId, $newResource);
    public function put($resourceName, $schema, $primaryKey, $resourceId, $newResource);
    public function delete($resourceName, $schema, $primaryKey, $resourceId);
    public function setDatabaseName($databaseName);
    public function getDatabaseName();
    public function getClient();
}
