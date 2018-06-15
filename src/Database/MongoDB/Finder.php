<?php
namespace Fathomminds\Rest\Database\MongoDB;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Finder as BaseFinder;
use MongoDB\Client;

class Finder extends BaseFinder
{
    private $filter;
    private $options;
    private $operatorMap = [
        'AND' => '$and',
        '&&' => '$and',
        'OR' => '$or',
        '||' => '$or',
        '==' => '$eq',
        '!=' => '$ne',
        '<' => '$lt',
        '>' => '$gt',
        '<=' => '$lte',
        '>=' => '$gte',
    ];
    public function __construct($client)
    {
        parent::__construct($client);
        $this->options = new FinderOptions;
    }

    protected function setLimit()
    {
        $this->options->limit = $this->queryConfiguration->limit;
    }

    protected function setOffset()
    {
        $this->options->skip = $this->queryConfiguration->offset;
    }

    protected function setOrderBy()
    {
        foreach ($this->queryConfiguration->orderBy as $orderBy) {
            $mongoSort = 1;
            switch (strtoupper(current($orderBy))) {
                case 'ASC':
                    $mongoSort = 1;
                    break;
                case 'DESC':
                    $mongoSort = -1;
                    break;
                default:
                    continue;
            }
            $this->options->sort[key($orderBy)] = $mongoSort;
        }
    }

    protected function setSelect()
    {
        if ($this->queryConfiguration->select !== '*' && is_array($this->queryConfiguration->select)) {
            /** @noinspection PhpWrongForeachArgumentTypeInspection */
            foreach ($this->queryConfiguration->select as $include) {
                $this->options->projection[$include] = 1;
            }
        }
    }

    protected function setWhere()
    {
        if (!empty($this->queryConfiguration->where)) {
            $this->filter = $this->parseWhere($this->queryConfiguration->where, $this->mainLogical);
            return;
        }
        $this->filter = [];
    }

    protected function parseWhere($conditions, $logical)
    {
        $subGroup = [];
        foreach ($conditions as $key => $condition) {
            switch (strtoupper($key)) {
                case 'AND':
                    $subGroup[] = $this->parseWhere($condition, 'AND');
                    break;
                case 'OR':
                    $subGroup[] = $this->parseWhere($condition, 'OR');
                    break;
                default:
                    list($fieldName, $operator, $value) = $condition;
                    if (array_key_exists($fieldName, $this->map)) {
                        try {
                            $value = $this->map[$fieldName]($value);
                        } catch (\Exception $ex) {
                            throw new RestException(
                                "Failed to map property",
                                [
                                    'propertyName' => $fieldName,
                                    'propertyValue' => $value
                                ]
                            );
                        }
                    }
                    $subGroup[] = [$fieldName => [$this->mapOperator($operator) => $value]];
            }
        }
        $currentGroup[$this->mapOperator($logical)] = $subGroup;
        return $currentGroup;
    }

    protected function parseWhereGroup($condition, $logical) {

    }

    protected function mapOperator($in)
    {
        if (!array_key_exists($in, $this->operatorMap)) {
            throw new RestException(
                'Invalid operator',
                [$in]
            );
        }
        return $this->operatorMap[$in];
    }

    protected function configQuery()
    {
        $this->setLimit();
        $this->setOffset();
        $this->setOrderBy();
        $this->setSelect();
        $this->setWhere();
    }

    public function get()
    {
        if (!($this->client instanceof Client)) {
            throw new RestException(
                'MongoDB Finder can be used with MongoDB Client only',
                ['type' => get_class($this->client)]
            );
        }
        $collection = $this->client
            ->selectDatabase($this->queryConfiguration->databaseName)
            ->selectCollection($this->queryConfiguration->from);
        $this->configQuery();
        $items = json_decode(
            json_encode(
                $collection->find(
                    $this->filter,
                    json_decode(json_encode($this->options), true)
                )->toArray()
            )
        );
        if (empty($items)) {
            $this->resultSet = [];
            return $this;
        }
        $this->resultSet = $items;
        return $this;
    }

    protected function createClient()
    {
        $this->client = new Client(Database::getUri(), Database::getUriOptions(), Database::getDriverOptions());
        return $this;
    }
}
