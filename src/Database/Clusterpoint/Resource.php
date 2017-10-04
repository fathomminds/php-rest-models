<?php
namespace Fathomminds\Rest\Database\Clusterpoint;

use Clusterpoint\Client;
use Fathomminds\Rest\Database\Clusterpoint\Connection;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Contracts\IResource;

class Resource implements IResource
{
    protected $MAX_LIMIT = 2147483647;
    protected $client;
    protected $databaseName;
    protected $collection;
    protected $resourceName;
    protected $primaryKey;

    public function __construct($resourceName, $primaryKey, Client $client = null, $databaseName = null)
    {
        $this->resourceName = $resourceName;
        $this->primaryKey = $primaryKey;
        $this->client = $client === null ? new Client : $client;
        $this->databaseName = $databaseName === null ? getenv('CLUSTERPOINT_DATABASE') : $databaseName;
        $this->collection = $this->client->database($this->databaseName . '.' . $this->resourceName);
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
        $res = $this->collection->find($resourceId);
        $this->failOnError($res);
        return $this->toObject($res);
    }

    protected function getAll()
    {
        $res = $this->collection->limit($this->MAX_LIMIT)->get();
        $this->failOnError($res);
        return $this->toObjectArray($res);
    }

    public function post($newResource)
    {
        try {
            $res = $this->collection->insertOne($newResource);
            $this->failOnError($res);
            $object = $this->toObject($res);
            $newResource->{$this->primaryKey} = empty($object->{$this->primaryKey}) ?
                null :
                $object->{$this->primaryKey};
            return $newResource;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    public function patch($resourceId, $newResource)
    {
        try {
            $res = $this->collection->update($resourceId, $newResource->__toArray());
            $this->failOnError($res);
            return $this->toObject($res);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    public function put($resourceId, $newResource)
    {
        try {
            $res = $this->collection->replace($resourceId, $newResource);
            $this->failOnError($res);
            return $this->toObject($res);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    public function delete($resourceId)
    {
        try {
            $res = $this->collection->delete($resourceId);
            $this->failOnError($res);
            return $this->toObject($res);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    protected function failOnError($res)
    {
        if (empty($res->error())) {
            return;
        }
        $message = $res->error()[0]->message === 'Requested document does not exist' ?
            'Resource does not exist' :
            'Database operation failed';
        throw new RestException(
            $message,
            [
                'error' => $res->error(),
                'res' => $res,
            ]
        );
    }

    protected function extractResult($cpResponse)
    {
        $res = json_decode($cpResponse->rawResponse());
        if (!property_exists($res, 'results')) {
            return null;
        }
        return $res->results;
    }

    protected function toObject($cpResponse)
    {
        $res = $this->extractResult($cpResponse);
        if (count($res) === 1) {
            return $res[0];
        }
        return new \StdClass;
    }

    protected function toObjectArray($cpResponse)
    {
        return $this->extractResult($cpResponse);
    }
}
