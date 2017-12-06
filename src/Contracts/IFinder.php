<?php
namespace Fathomminds\Rest\Contracts;

interface IFinder
{
    /**
     * @param $databaseName
     * @return IFinder
     *
     */
    public function database($databaseName);

    /**
     * @param $fieldNamesArray
     * @return IFinder
     */
    public function select($fieldNamesArray);

    /**
     * @param $collectionName
     * @return IFinder
     */
    public function from($collectionName);

    /**
     * @param $whereConditions
     * @return IFinder
     */
    public function where($whereConditions);

    /**
     * @param $fieldName
     * @param string $sortMode
     * @return IFinder
     */
    public function orderBy($fieldName, $sortMode = 'ASC');

    /**
     * @param $limit
     * @return IFinder
     */
    public function limit($limit);

    /**
     * @param $offset
     * @return IFinder
     */
    public function offset($offset);

    /**
     * @return IFinder
     */
    public function get();

    /**
     * @return mixed
     */
    public function first();

    /**
     * @return array
     */
    public function all();
}
