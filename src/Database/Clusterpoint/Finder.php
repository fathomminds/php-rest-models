<?php
namespace Fathomminds\Rest\Database\Clusterpoint;

use Fathomminds\Rest\Database\Finder as BaseFinder;
use Clusterpoint\Client;

class Finder extends BaseFinder
{
    public function get()
    {
        $collection = $this->client->database($this->databaseName.'.'.$this->from);
        if ($this->limit !== null) {
            $collection->limit($this->limit);
        }
        if ($this->offset !== null) {
            $collection->offset($this->offset);
        }
        foreach ($this->orderBy as $orderBy) {
            $collection->orderBy(key($orderBy), current($orderBy));
        }
        if ($this->select !== '*') {
            $collection->select($this->select);
        }
        if (!empty($this->where)) {
            $this->parseWhere($collection, $this->where, $this->mainLogical);
        }
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

    private function parseWhere($collection, $conditions, $logical)
    {
        foreach ($conditions as $key => $condition) {
            switch (strtoupper($key)) {
                case 'AND':
                    if ($logical === '||') {
                        $collection->orWhere($this->addWhereGroup($collection, $condition, '&&'));
                    }
                    if ($logical === '&&') {
                        $collection->where($this->addWhereGroup($collection, $condition, '&&'));
                    }
                    break;
                case 'OR':
                    if ($logical === '||') {
                        $collection->orWhere($this->addWhereGroup($collection, $condition, '||'));
                    }
                    if ($logical === '&&') {
                        $collection->where($this->addWhereGroup($collection, $condition, '||'));
                    }
                    break;
                default:
                    list($fieldName, $operator, $value) = $condition;
                    $collection->where($fieldName, $operator, $value, $logical);
            }
        }
    }
}
