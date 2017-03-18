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

    public function get($resourceName, $resourceId = null)
    {
        return (new Resource(
            $resourceName,
            $this->client,
            $this->databaseName
        ))->get($resourceId);
    }

    public function post($resourceName, $newResource)
    {
        return (new Resource(
            $resourceName,
            $this->client,
            $this->databaseName
        ))->post($newResource);
    }

    public function put($resourceName, $resourceId, $newResource)
    {
        return (new Resource(
            $resourceName,
            $this->client,
            $this->databaseName
        ))->put($resourceId, $newResource);
    }

    public function delete($resourceName, $resourceId)
    {
        return (new Resource(
            $resourceName,
            $this->client,
            $this->databaseName
        ))->delete($resourceId);
    }

    public function getCollection($resourceName)
    {
        return (new Resource(
            $resourceName,
            $this->client,
            $this->databaseName
        ))->getCollection();
    }
}
