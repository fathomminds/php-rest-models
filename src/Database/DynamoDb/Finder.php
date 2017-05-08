<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Fathomminds\Rest\Database\Finder as BaseFinder;
use Aws\DynamoDb\DynamoDbClient as Client;
use Aws\Sdk;

class Finder extends BaseFinder
{
    public function get()
    {
        $this->resultSet = [];
        return $this;
    }

    protected function createClient()
    {
        $sdk = new Sdk([
            'region' => getenv('AWS_SDK_REGION'),
            'version' => getenv('AWS_SDK_VERSION'),
            'http' => [
                'verify' => getenv('AWS_SDK_HTTP_VERIFY') === 'false' ? false : getenv('AWS_SDK_HTTP_VERIFY'),
            ]
        ]);
        $this->client = $sdk->createDynamoDb();
        return $this;
    }
}
