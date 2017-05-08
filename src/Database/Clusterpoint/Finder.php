<?php
namespace Fathomminds\Rest\Database\Clusterpoint;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Finder as BaseFinder;
use Clusterpoint\Client;

class Finder extends BaseFinder
{
    protected function setLimit($collection)
    {
        if ($this->queryConfiguration->limit !== null) {
            $collection->limit($this->queryConfiguration->limit);
        }
    }

    protected function setOffset($collection)
    {
        if ($this->queryConfiguration->offset !== null) {
            $collection->offset($this->queryConfiguration->offset);
        }
    }

    protected function setOrderBy($collection)
    {
        foreach ($this->queryConfiguration->orderBy as $orderBy) {
            $collection->orderBy(key($orderBy), current($orderBy));
        }
    }

    protected function setSelect($collection)
    {
        if ($this->queryConfiguration->select !== '*') {
            $collection->select($this->queryConfiguration->select);
        }
    }

    public function setWhere($collection)
    {
        if (!empty($this->queryConfiguration->where)) {
            $this->parseWhere($collection, $this->queryConfiguration->where, $this->mainLogical);
        }
    }

    public function get()
    {
        if (!($this->client instanceof Client)) {
            throw new RestException(
                'Clusterpoint Finder can be used with Clusterpoint Client only',
                ['type' => get_class($this->client)]
            );
        }
        $collection = $this->client->database(
            $this->queryConfiguration->databaseName.
            '.'.
            $this->queryConfiguration->from
        );
        $this->setLimit($collection);
        $this->setOffset($collection);
        $this->setOrderBy($collection);
        $this->setSelect($collection);
        $this->setWhere($collection);
        $items = json_decode(json_encode($collection->get()->toArray()));
        $c = count($items);
        for ($i=0; $i<$c; $i++) {
            $this->resultSet[] = $items[$i];
        }
        return $this;
    }

    protected function createClient()
    {
        $this->client = new Client;
        return $this;
    }

    protected function addWhereGroup($collection, $conditions, $logical)
    {
      /**
      * @codeCoverageIgnore
      * Passing anonymous function to Clusterpoint API
      */
        return function ($collection) use ($conditions, $logical) {
            $this->parseWhere($collection, $conditions, $logical);
        };
    }

    protected function parseWhereGroup($logical, $collection, $condition, $nextLogical)
    {
        if ($logical === '||') {
            $collection->orWhere($this->addWhereGroup($collection, $condition, $nextLogical));
        }
        if ($logical === '&&') {
            $collection->where($this->addWhereGroup($collection, $condition, $nextLogical));
        }
    }

    private function parseWhere($collection, $conditions, $logical)
    {
        foreach ($conditions as $key => $condition) {
            switch (strtoupper($key)) {
                case 'AND':
                    $this->parseWhereGroup($logical, $collection, $condition, '&&');
                    break;
                case 'OR':
                    $this->parseWhereGroup($logical, $collection, $condition, '||');
                    break;
                default:
                    list($fieldName, $operator, $value) = $condition;
                    $collection->where($fieldName, $operator, $value, $logical);
            }
        }
    }
}
