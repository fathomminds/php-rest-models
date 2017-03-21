<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\Sdk;
use Aws\DynamoDb\DynamoDbClient;
use Fathomminds\Rest\Contracts\IDatabase;
use Fathomminds\Rest\Database\DynameDb\Resource;
use Fathomminds\Rest\Helpers\ReflectionHelper;

class Database implements IDatabase
{
    protected $client;
    protected $databaseName;

    public function __construct(DynamoDbClient $client = null, $databaseName = null)
    {
        $this->client = $client === null ? $this->createClient() : $client;
        $this->databaseName = $databaseName === null ? $this->getFullDatabaseName() : $databaseName;
    }

    private function createClient()
    {
        $sdk = new Sdk([
            'region' => getenv('AWS_SDK_REGION'),
            'version' => getenv('AWS_SDK_VERSION'),
            'http' => [
                'verify' => getenv('AWS_SDK_HTTP_VERIFY')
            ]
        ]);
        return $sdk->createDynamoDb();
    }

    private function getFullDatabaseName()
    {
        return getenv('AWS_DYNAMODB_NAMESPACE') . '-' . getenv('AWS_DYNAMODB_DATABASE');
    }

    public function get($resourceName, $primaryKey, $resourceId = null)
    {
        return (new Resource(
            $resourceName,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->get($resourceId);
    }

    public function post($resourceName, $primaryKey, $newResource)
    {
        return (new Resource(
            $resourceName,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->post($newResource);
    }

    public function put($resourceName, $primaryKey, $resourceId, $newResource)
    {
        return (new Resource(
            $resourceName,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->put($resourceId, $newResource);
    }

    public function delete($resourceName, $primaryKey, $resourceId)
    {
        return (new Resource(
            $resourceName,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->delete($resourceId);
    }

    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    public function getClient()
    {
        return $this->client;
    }
}
