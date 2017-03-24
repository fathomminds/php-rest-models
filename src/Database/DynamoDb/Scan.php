<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

class Scan extends DatabaseOperation
{
    protected function execute($query)
    {
        return $this->client->scan($query);
    }
}
