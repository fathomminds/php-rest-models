<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

class Query extends DatabaseOperation
{
    protected function execute($query)
    {
        return $this->client->query($query);
    }
}
