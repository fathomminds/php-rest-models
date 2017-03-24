<?php
namespace Fathomminds\Rest\Database\Clusterpoint;

use Clusterpoint\Client;
use Fathomminds\Rest\Contracts\IDatabase;
use Fathomminds\Rest\Database\Clusterpoint\Resource;
use Fathomminds\Rest\Helpers\ReflectionHelper;

class Database implements IDatabase
{
    protected $client;
    protected $databaseName;

    public function __construct(Client $client = null, $databaseName = null)
    {
        $this->client = $client === null ? new Client : $client;
        $this->databaseName = $databaseName === null ? getenv('CLUREXID_CLUSTERPOINT_DATABASE') : $databaseName;
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
