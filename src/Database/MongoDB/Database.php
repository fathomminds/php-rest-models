<?php
namespace Fathomminds\Rest\Database\MongoDB;

use MongoDB\Client;
use Fathomminds\Rest\Contracts\IDatabase;

/**
 *
 * @property \MongoDB\Client $client
 * @property string $databaseName
 *
 */

class Database implements IDatabase
{
    protected $client;
    protected $databaseName;

    public function __construct(Client $client = null, $databaseName = null)
    {
        $this->client = $client === null ?
            new Client(self::getUri(), self::getUriOptions(), self::getDriverOptions()) :
            $client;
        $this->databaseName = $databaseName === null ? getenv('MONGODB_DATABASE') : $databaseName;
    }

    public static function getUri() {
        return 'mongodb://' .
            getenv('MONGODB_USERNAME') . ':' .
            getenv('MONGODB_PASSWORD') . '@' .
            getenv('MONGODB_HOST') . '/' .
            getenv('MONGODB_AUTH_DATABASE');
    }

    public static function getUriOptions() {
        return [];
    }

    public static function getDriverOptions() {
        return [];
    }

    public function get($resourceName, $schema, $primaryKey, $resourceId = null)
    {
        return (new Resource(
            $resourceName,
            $schema,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->get($resourceId);
    }

    public function post($resourceName, $schema, $primaryKey, $newResource)
    {
        return (new Resource(
            $resourceName,
            $schema,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->post($newResource);
    }

    public function patch($resourceName, $schema, $primaryKey, $resourceId, $newResource)
    {
        return (new Resource(
            $resourceName,
            $schema,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->patch($resourceId, $newResource);
    }

    public function put($resourceName, $schema, $primaryKey, $resourceId, $newResource)
    {
        return (new Resource(
            $resourceName,
            $schema,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->put($resourceId, $newResource);
    }

    public function delete($resourceName, $schema, $primaryKey, $resourceId)
    {
        return (new Resource(
            $resourceName,
            $schema,
            $primaryKey,
            $this->client,
            $this->databaseName
        ))->delete($resourceId);
    }

    public function setDatabaseName($databaseName)
    {
        $this->databaseName = $databaseName;
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
