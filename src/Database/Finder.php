<?php
namespace Fathomminds\Rest\Database;

use Fathomminds\Rest\Contracts\IFinder;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Where;

abstract class Finder implements IFinder
{
    protected $queryConfiguration;
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
            $this->validateCondition($condition);
        }
    }

    private function validateCondition($condition)
    {
        if (count($condition) === 3) {
            $operator = $condition[1];
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

    public function __construct($client = null)
    {
        $this->queryConfiguration = new FinderQueryConfiguration;
        if ($client !== null) {
            $this->client = $client;
            return $this;
        }
        $this->createClient();
    }

    public function database($databaseName)
    {
        $this->queryConfiguration->databaseName = $databaseName;
        return $this;
    }

    public function select($fieldList)
    {
        $this->queryConfiguration->select = $fieldList;
        return $this;
    }

    public function from($collectionName)
    {
        $this->queryConfiguration->from = $collectionName;
        return $this;
    }

    public function where($conditions)
    {
        $this->validateConditions($conditions);
        $this->queryConfiguration->where = $conditions;
        return $this;
    }

    public function orderBy($fieldName, $sortMode = 'ASC')
    {
        $this->queryConfiguration->orderBy[] = [$fieldName => $sortMode];
        return $this;
    }

    public function limit($limit)
    {
        $this->queryConfiguration->limit = $limit;
        return $this;
    }

    public function offset($offset)
    {
        $this->queryConfiguration->offset = $offset;
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
