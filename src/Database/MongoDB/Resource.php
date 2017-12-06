<?php
namespace Fathomminds\Rest\Database\MongoDB;

use MongoDB\Client;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Contracts\IResource;

class Resource implements IResource
{
    protected $client;
    protected $databaseName;
    protected $collection;
    protected $resourceName;
    protected $primaryKey;

    public function __construct($resourceName, $primaryKey, Client $client = null, $databaseName = null)
    {
        $this->resourceName = $resourceName;
        $this->primaryKey = $primaryKey;
        $this->client = $client === null ?
            new Client($this->getUri(), $this->getUriOptions(), $this->getDriverOptions()) :
            $client;
        $this->databaseName = $databaseName === null ? getenv('MONGODB_DATABASE') : $databaseName;
        $mongodb = $this->client->selectDatabase($this->databaseName);
        $this->collection = $mongodb->selectCollection($this->resourceName);
    }

    protected function getUri() {
        return 'mongodb://' .
        getenv('MONGODB_USERNAME') . ':' .
        getenv('MONGODB_PASSWORD') . '@' .
        getenv('MONGODB_HOST');
    }

    protected function getUriOptions() {
        return [];
    }

    protected function getDriverOptions() {
        return [];
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
        $res = $this->collection->findOne([$this->primaryKey => $resourceId]);
        return json_decode(json_encode($res));
    }

    protected function getAll()
    {
        $res = $this->collection->find();
        return json_decode(json_encode($res->toArray()));
    }

    public function post($newResource)
    {
        try {
            $res = $this->collection->insertOne($newResource);
            $newResource->{$this->primaryKey} = $res->getInsertedId();
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
            $this->collection->updateOne(
                [$this->primaryKey => $resourceId],
                ['$set' => get_object_vars($newResource)]
            );
            $newResource->{$this->primaryKey} = $resourceId;
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
            $this->collection->replaceOne(
                [$this->primaryKey => $resourceId],
                $newResource
            );
            $newResource->{$this->primaryKey} = $resourceId;
            return $newResource;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), []);
        }
    }

    public function delete($resourceId)
    {
        try {
            $this->collection->deleteOne([$this->primaryKey => $resourceId]);
            return null;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), []);
        }
    }
}
