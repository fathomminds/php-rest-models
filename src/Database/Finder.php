<?php
namespace Fathomminds\Rest\Database;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Where;

abstract class Finder
{
    protected $databaseName;
    protected $select = '*';
    protected $from;
    protected $where;
    protected $orderBy = [];
    protected $limit;
    protected $offset;
    protected $resultSet;
    protected $client;
    protected $mainLogical = '&&';
    protected $validGroups = [
        'AND',
        'OR',
    ];
    protected $validOperators = [
        '==',
        '!=',
        '<',
        '>',
        '<=',
        '>=',
    ];

    abstract protected function createClient();
    abstract public function get();

    private function validateConditions($conditions)
    {
        foreach ($conditions as $key => $condition) {
            if (in_array(strtoupper($key), $this->validGroups)) {
                $this->validateConditions($condition);
                return;
            }
            if (count($condition) === 3) {
                list($fieldName, $operator, $fieldValue) = $condition;
                if (!in_array($operator, $this->validOperators)) {
                    throw new RestException('Invalid operator in where condition', [
                        'operator' => $operator,
                    ]);
                }
                return;
            }
            throw new RestException('Invalid where condition', [
                'condition' => $condition,
            ]);
        }
    }

    public function __construct($client = null)
    {
        if ($client !== null) {
            $this->client = $client;
            return $this;
        }
        $this->createClient();
    }

    public function database($databaseName)
    {
        $this->databaseName = $databaseName;
        return $this;
    }

    public function select($fieldList)
    {
        $this->select = $fieldList;
        return $this;
    }

    public function from($collectionName)
    {
        $this->from = $collectionName;
        return $this;
    }

    public function where($conditions)
    {
        $this->validateConditions($conditions);
        $this->where = $conditions;
        return $this;
    }

    public function orderBy($fieldName, $sortMode)
    {
        $this->orderBy[] = [$fieldName => $sortMode];
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    public function first()
    {
        return isset($this->resultSet[0]) ? $this->resultSet[0] : null;
    }

    public function all()
    {
        return $this->resultSet;
    }
}
