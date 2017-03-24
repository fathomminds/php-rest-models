<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

abstract class DatabaseOperation
{
    protected $client;
    protected $lastEvaluatedKey;
    protected $query;
    protected $result;
    protected $first = true;

    public function __construct(DynamoDbClient $client, $query)
    {
        $this->client = $client;
        $this->query = $query;
    }

    public function next()
    {
        if (!$this->first && $this->lastEvaluatedKey === null) {
            return null;
        }
        $this->first = false;
        $query = $this->setExclusiveStartKey($this->query);
        $result = $this->execute($query);
        $this->setLastEvaluatedKey($result);
        return $result;
    }

    abstract protected function execute($query);

    protected function setExclusiveStartKey($query)
    {
        if ($this->lastEvaluatedKey !== null) {
            $query['ExclusiveStartKey'] = $this->lastEvaluatedKey;
        }
        return $query;
    }

    protected function setLastEvaluatedKey($result)
    {
        $this->lastEvaluatedKey = null;
        if (!empty($result['LastEvaluatedKey'])) {
            $this->lastEvaluatedKey = $result['LastEvaluatedKey'];
        }
    }
}
