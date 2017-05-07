<?php
namespace Fathomminds\Rest\Contracts;

interface IFinder
{
    public function database($databaseName);
    public function select($fieldNamesArray);
    public function from($collectionName);
    public function where($whereConditions);
    public function orderBy($fieldName, $sortMode = 'ASC');
    public function limit($limit);
    public function offset($offset);
    public function get();
    public function first();
    public function all();
}
