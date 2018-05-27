<?php
namespace Fathomminds\Rest\Database\MongoDB;

use MongoDB\Client;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Contracts\IResource;
use Fathomminds\Rest\Helpers\ReflectionHelper;
use Fathomminds\Rest\Schema\TypeValidators\MongoIdValidator;

class Resource implements IResource
{
    protected $client;
    protected $databaseName;
    protected $collection;
    protected $resourceName;
    protected $primaryKey;

    protected $schema;

    public function __construct($resourceName, $schemaClass, $primaryKey, Client $client = null, $databaseName = null)
    {
        $reflectionHelper = new ReflectionHelper;
        $this->resourceName = $resourceName;
        $this->schema = $reflectionHelper->createInstance($schemaClass)->schema();
        $this->primaryKey = $primaryKey;
        $this->client = $client === null ?
            new Client(Database::getUri(), Database::getUriOptions(), Database::getDriverOptions()) :
            $client;
        $this->databaseName = $databaseName === null ? getenv('MONGODB_DATABASE') : $databaseName;
        $mongodb = $this->client->selectDatabase($this->databaseName);
        $this->collection = $mongodb->selectCollection($this->resourceName);
    }

    public function get($resourceId = null)
    {
        if ($resourceId !== null) {
            return $this->getOne($resourceId);
        }
        return $this->getAll();
    }

    protected function getOne($resourceId)
    {
        $mongoIdCast = new MongoIdCast();
        $res = $this->collection->findOne([
            $this->primaryKey => $mongoIdCast->castSingleValueToMongoId(
                $this->primaryKey,
                $resourceId,
                $this->schema
            )
        ]);
        $bsonSerialize = new BSONSerialize();
        return $bsonSerialize->serializeObject($res);
    }

    protected function getAll()
    {
        $bsonSerialize = new BSONSerialize();
        $res = iterator_to_array($this->collection->find());
        foreach ($res as $resIdx => $resItem) {
            $res[$resIdx] = $bsonSerialize->serializeObject($resItem, $this->schema);
        }
        return $res;
    }

    public function post($newResource)
    {
        try {
            $mongoIdCast = new MongoIdCast();
            $res = $this->collection->insertOne(
                $mongoIdCast->castToMongoId(
                    $newResource,
                    $this->schema
                )
            );
            $newResource->{$this->primaryKey} = $res->getInsertedId();
            $newResource = $mongoIdCast->castToString(
                $newResource,
                $this->schema
            );
            return $newResource;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), []);
        }
    }

    public function patch($resourceId, $newResource)
    {
        try {
            if (isset($newResource->{$this->primaryKey})) {
                unset($newResource->{$this->primaryKey});
            }
            $mongoIdCast = new MongoIdCast();
            $mogoUpdateSerialize = new MongoUpdateSerialize();
            $this->collection->updateOne(
                [$this->primaryKey => $mongoIdCast->castSingleValueToMongoId(
                    $this->primaryKey,
                    $resourceId,
                    $this->schema
                )],
                ['$set' => get_object_vars(
                    $mogoUpdateSerialize->serialize(
                        $mongoIdCast->castToMongoId($newResource, $this->schema)
                    )
                )]
            );
            $newResource->{$this->primaryKey} = $resourceId;
            $newResource = $mongoIdCast->castToString(
                $newResource,
                $this->schema
            );
            return $newResource;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), []);
        }
    }

    public function put($resourceId, $newResource)
    {
        try {
            if (isset($newResource->{$this->primaryKey})) {
                unset($newResource->{$this->primaryKey});
            }
            $mongoIdCast = new MongoIdCast();
            $this->collection->replaceOne(
                [$this->primaryKey => $mongoIdCast->castSingleValueToMongoId(
                    $this->primaryKey,
                    $resourceId,
                    $this->schema
                )],
                $mongoIdCast->castToMongoId($newResource, $this->schema)
            );
            $newResource->{$this->primaryKey} = $resourceId;
            $newResource = $mongoIdCast->castToString(
                $newResource,
                $this->schema
            );
            return $newResource;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), []);
        }
    }

    public function delete($resourceId)
    {
        try {
            $mongoIdCast = new MongoIdCast();
            $this->collection->deleteOne([$this->primaryKey => $mongoIdCast->castSingleValueToMongoId(
                $this->primaryKey,
                $resourceId,
                $this->schema
            )]);
            return null;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), []);
        }
    }
}
