<?php
namespace Fathomminds\Clurexid\Rest\Database\Clusterpoint;

use \Exception;
use \StdClass;
use Clusterpoint\Client;
use Fathomminds\Clurexid\Rest\Database\Clusterpoint\Connection;
use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;
use Fathomminds\Clurexid\Rest\Contracts\IResource;

class Resource implements IResource
{
    protected $client;
    protected $databaseName;
    protected $collection;
    protected $resourceName;

    public function __construct($resourceName, Client $client = null, $databaseName = null)
    {
        $this->resourceName = $resourceName;
        $this->client = $client === null ? new Client : $client;
        $this->databaseName = $databaseName === null ? getenv('CLUREXID_CLUSTERPOINT_DATABASE') : $databaseName;
        $this->collection = $this->client->database($this->databaseName . '.' . $this->resourceName);
    }

    public function get($resourceId = null)
    {
        try {
            $res = $resourceId === null ? $this->collection->get() : $this->collection->find($resourceId);
            $this->failOnError($res);
            return $this->toObject($res, $resourceId === null ? 'array' : 'object');
        } catch (Exception $ex) {
            throw new DetailedException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    public function post($newResource)
    {
        try {
            $res = $this->collection->insertOne($newResource);
            $this->failOnError($res);
            return $this->toObject($res);
        } catch (Exception $ex) {
            throw new DetailedException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    public function put($resourceId, $newResource)
    {
        try {
            $res = $this->collection->update($resourceId, $newResource);
            $this->failOnError($res);
            return $this->toObject($res);
        } catch (Exception $ex) {
            throw new DetailedException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    public function delete($resourceId)
    {
        try {
            $res = $this->collection->delete($resourceId);
            $this->failOnError($res);
            return $this->toObject($res);
        } catch (Exception $ex) {
            throw new DetailedException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    public function getCollection()
    {
        return $this->collection;
    }

    protected function failOnError($res)
    {
        if (empty($res->error())) {
            return;
        }
        throw new DetailedException(
            'Database operation failed',
            $res->error()
        );
    }

    protected function toObject($cpResponse, $emptyType = 'object')
    {
        $res = json_decode($cpResponse->rawResponse());
        if (!property_exists($res, 'results')) {
            return null;
        }
        $res = $res->results;
        if (count($res) === 1) {
            $res = $res[0];
        }
        if (count($res) === 0) {
            $res = $emptyType === 'array' ? [] : new StdClass;
        }
        return $res;
    }
}
