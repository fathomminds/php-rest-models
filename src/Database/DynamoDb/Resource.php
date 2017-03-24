<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\Sdk;
use Aws\DynamoDb\DynamoDbClient as Client;
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

    public function __construct($resourceName, $primaryKey, Client $client = null, $databaseName = null)
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
        return $this->unmarshalBatch($res['Items']);
    }

    protected function createScanQuery()
    {
        $query = [
            'TableName' => $this->fullTableName,
        ];
        return $query;
    }

    protected function throwAwsPostError($ex)
    {
        switch ($ex->getAwsErrorCode()) {
            case 'ConditionalCheckFailedException':
                throw new RestException(
                    'Primary key collision',
                    ['exception' => $ex]
                );
        }
        throw new RestException($ex->getMessage(), ['exception' => $ex]);
    }

    public function post($newResource)
    {
        try {
            $query = $this->createPostQuery($newResource);
            $this->client->putItem($query);
        } catch (DynamoDbException $ex) {
            $this->throwAwsPostError($ex);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['exception' => $ex]);
        }
        return $newResource;
    }

    protected function createPostQuery($newResource)
    {
        $query = [
            'TableName' => $this->fullTableName,
            'Item' => $this->marshalItem($newResource),
            'ConditionExpression' => 'attribute_not_exists(#pk)',
            "ExpressionAttributeNames" => ["#pk" => $this->primaryKey],
        ];
        return $query;
    }

    protected function throwAwsPutError($ex)
    {
        switch ($ex->getAwsErrorCode()) {
            case 'ConditionalCheckFailedException':
                throw new RestException(
                    'Resource does not exist',
                    ['exception' => $ex]
                );
        }
        throw new RestException($ex->getMessage(), ['exception' => $ex]);
    }

    public function put($resourceId, $newResource)
    {
        try {
            $newResource->{$this->primaryKey} = $resourceId;
            $query = $this->createPutQuery($newResource);
            $res = $this->client->putItem($query);
        } catch (DynamoDbException $ex) {
            $this->throwAwsPutError($ex);
        } catch (\Exception $ex) {
            throw new RestException($ex->getMessage(), ['result'=>empty($res)?null:$res]);
        }
        return $newResource;
    }

    protected function createPutQuery($newResource)
    {
        $query = [
            'TableName' => $this->fullTableName,
            'Item' => $this->marshalItem($newResource),
            'ConditionExpression' => 'attribute_exists(#pk)',
            "ExpressionAttributeNames" => ["#pk" => $this->primaryKey],
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
        if ($item === null) {
            return new \StdClass;
        }
        return $this->marshaler->unmarshalItem($item, true);
    }

    protected function unmarshalBatch($itemList)
    {
        if ($itemList === null) {
            return [];
        }
        $list = [];
        foreach ($itemList as $item) {
            $list[] = $this->unmarshalItem($item, true);
        }
        return $list;
    }
}
