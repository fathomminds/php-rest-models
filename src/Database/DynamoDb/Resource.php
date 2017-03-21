<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\Sdk;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Contracts\IResource;
use Fathomminds\Rest\Helpers\Uuid;
use Aws\DynamoDb\Exception\DynamoDbException;

class Resource implements IResource
{
    protected $client;
    protected $databaseName;
    protected $collection;
    protected $fullTableName;
    protected $resourceName;
    protected $primaryKey;
    protected $marshaler;
    protected $retryCount = 0;

    public function __construct($resourceName, $primaryKey = null, Client $client = null, $databaseName = null)
    {
        $this->resourceName = $resourceName;
        $this->primaryKey = $primaryKey;
        $this->client = $client === null ? $this->createClient() : $client;
        $this->databaseName = $databaseName === null ? $this->getFullDatabaseName() : $databaseName;
        $this->fullTableName = $this->databaseName . '-' . $this->resourceName;
        $this->marshaler = new Marshaler;
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
        $query = $this->createGetQuery($resourceId);
        $res = $this->client->getItem($query);
        return $this->unmarshalItem($res['Item']);
    }

    protected function createGetQuery($resourceId)
    {
        $query = [
            'TableName' => $this->fullTableName,
            'Key' => $this->marshalItem([$this->primaryKey => $resourceId]),
        ];
        return $query;
    }

    protected function getAll()
    {
        $query = $this->createScanQuery();
        $res = $this->client->scan($query);
        return $res['Items'];
    }

    protected function createScanQuery()
    {
        $query = [
            'TableName' => $this->fullTableName,
        ];
        return $query;
    }

    public function post($newResource)
    {
        try {
            $query = $this->createPostQuery($newResource);
            $res = $this->client->putItem($query);
            return $newResource;
        } catch (DynamoDbException $ex) {
            if ($ex->getAwsErrorCode() === 'ConditionalCheckFailedException') {
                return $this->retryPost($newResource, $this->retryCount);
            }
            throw new RestException($ex->getMessage(), ['exception'=>$ex]);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['exception'=>$ex]);
        }
    }

    protected function retryPost($newResource, $counter)
    {
        $this->retryCount += 1;
        if ($counter > 5) {
            $this->retryCount = 0;
            throw new RestException('Creating new resource failed', [
                'retryCount' => $counter,
                'resource' => $newResource,
            ]);
        }
        $this->post($newResource);
        return $newResource;
    }

    protected function createPostQuery($newResource)
    {
        $createResource = $newResource;
        $createResource->id = (new Uuid)->generate();
        $query = [
            'TableName' => $this->fullTableName,
            'Item' => $this->marshalItem($newResource),
            'ConditionExpression' => 'attribute_not_exists('.$this->primaryKey.')',
        ];
        return $query;
    }

    public function put($resourceId, $newResource)
    {
        try {
            $newResource->{$this->primaryKey} = $resourceId;
            $query = $this->createPutQuery($newResource);
            $res = $this->client->putItem($query);
            return $newResource;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    protected function createPutQuery($newResource)
    {
        $query = [
            'TableName' => $this->fullTableName,
            'Item' => $this->marshalItem($newResource),
            'ConditionExpression' => 'attribute_exists('.$this->primaryKey.')',
        ];
        return $query;
    }

    public function delete($resourceId)
    {
        try {
            $query = $this->createDeleteQuery($resourceId);
            $res = $this->client->deleteItem($query);
            return $resourceId;
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
    }

    protected function createDeleteQuery($resourceId)
    {
        $query = [
            'TableName' => $this->fullTableName,
            'Key' => $this->marshalItem([$this->primaryKey => $resourceId]),
        ];
        return $query;
    }

    protected function marshalItem($resource)
    {
        return $this->marshaler->marshalItem($resource);
    }

    protected function unmarshalItem($item)
    {
        return $this->marshaler->unmarshalItem($item);
    }
}
