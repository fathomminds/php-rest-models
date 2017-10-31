<?php
namespace Fathomminds\Rest\Contracts;

interface IRestObject
{
    public function createFromObject($obj);
    public function setDatabaseName($databaseName);
    public function getDatabaseName();
    public function resource();
    public function getResourceName();
    public function get($resourceId = null);
    public function post($newResource);
    public function patch($resourceId, $newResource);
    public function put($resourceId, $newResource);
    public function delete($resourceId);
    public function reset();
    public function replaceMode($value = null);
    public function updateMode($value = null);
    public function setFieldDefaults();
    public function validateSchema($resource);
    public function validate();
    public function toArray();
    public function getPrimaryKeyValue();
    public function getUniqueFields();
    public function validateUniqueFields();
    public function find();
    public function query();

}
