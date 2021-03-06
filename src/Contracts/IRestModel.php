<?php
namespace Fathomminds\Rest\Contracts;

interface IRestModel
{
    public function resource($resource = null);
    public function setDatabaseName($databaseName);
    public function getDatabaseName();
    public function query();
    public function find();
    public function one($resourceId);
    public function all();
    public function create();
    public function update();
    public function replace();
    public function delete();
    public function validate();
    public function toArray();
}
