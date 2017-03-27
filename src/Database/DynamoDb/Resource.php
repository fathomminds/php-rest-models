<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\Sdk;
use Aws\DynamoDb\DynamoDbClient as Client;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Contracts\IResource;
use Fathomminds\Rest\Helpers\Uuid;
use Aws\DynamoDb\Exception\DynamoDbException;
use Fathomminds\Rest\Database\DynamoDb\QueryConstructor;

class Resource implements IResource
{
    protected $client;
    protected $databaseName;
    protected $collection;
    protected $fullTableName;
    protected $resourceName;
    protected $primaryKey;
    protected $queryConstructor;

    public function __construct($resourceName, $primaryKey, Client $client = null, $databaseName = null)
    {
        $this->resourceName = $resourceName;
        $this->primaryKey = $primaryKey;
        $this->client = $client === null ? $this->createClient() : $client;
        $this->databaseName = $databaseName === null ? $this->getFullDatabaseName() : $databaseName;
        $this->fullTableName = $this->databaseName . '-' . $this->resourceName;
        $this->queryConstructor = new QueryConstructor;
    }

    private function createClient()
    {
        $sdk = new Sdk([
            'region'   => getenv('AWS_SDK_REGION'),
            'version'  => getenv('AWS_SDK_VERSION'),
            'http' => [
                'verify' => getenv('AWS_SDK_HTTP_VERIFY') === 'false' ? false : getenv('AWS_SDK_HTTP_VERIFY'),
            ]
        ]);
        return $sdk->createDynamoDb();
    }

    private function getFullDatabaseName()
    {
        return getenv('AWS_DYNAMODB_NAMESPACE') . '-' . getenv('AWS_DYNAMODB_DATABASE');
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
        $query = $this->queryConstructor->createGetQuery($this->fullTableName, $this->primaryKey, $resourceId);
        $res = $this->client->getItem($query);
        return $this->queryConstructor->unmarshalItem($res['Item']);
    }

    protected function getAll()
    {
        $query = $this->queryConstructor->createScanQuery($this->fullTableName);
        $res = $this->client->scan($query);
        return $this->queryConstructor->unmarshalBatch($res['Items']);
    }

    protected function throwAwsPostError($exception)
    {
        switch ($exception->getAwsErrorCode()) {
            case 'ConditionalCheckFailedException':
                throw new RestException(
                    'Primary key collision',
                    ['exception' => $exception]
                );
        }
        throw new RestException($exception->getMessage(), ['exception' => $exception]);
    }

    public function post($newResource)
    {
        try {
            $query = $this->queryConstructor->createPostQuery($this->fullTableName, $this->primaryKey, $newResource);
            $this->client->putItem($query);
        } catch (DynamoDbException $ex) {
            $this->throwAwsPostError($ex);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['exception' => $ex]);
        }
        return $newResource;
    }

    protected function throwAwsPutError($exception)
    {
        switch ($exception->getAwsErrorCode()) {
            case 'ConditionalCheckFailedException':
                throw new RestException(
                    'Resource does not exist',
                    ['exception' => $exception]
                );
        }
        throw new RestException($exception->getMessage(), ['exception' => $exception]);
    }

    public function put($resourceId, $newResource)
    {
        try {
            $newResource->{$this->primaryKey} = $resourceId;
            $query = $this->queryConstructor->createPutQuery($this->fullTableName, $this->primaryKey, $newResource);
            $res = $this->client->putItem($query);
        } catch (DynamoDbException $ex) {
            $this->throwAwsPutError($ex);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
        return $newResource;
    }

    public function delete($resourceId)
    {
        try {
            $query = $this->queryConstructor->createDeleteQuery($this->fullTableName, $this->primaryKey, $resourceId);
            $res = $this->client->deleteItem($query);
            return $resourceId;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }
}
