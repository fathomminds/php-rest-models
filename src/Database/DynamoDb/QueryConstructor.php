<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;

class QueryConstructor
{
    public function marshalItem($resource)
    {
        $marshaler = new Marshaler;
        $toMarshal = $resource;
        if (gettype($resource) === 'object') {
            $toMarshal = new \StdClass;
            foreach (get_object_vars($resource) as $name => $value) {
                $toMarshal->$name = $value;
            }
        }
        return $marshaler->marshalItem($toMarshal);
    }

    public function unmarshalItem($item)
    {
        $marshaler = new Marshaler;
        if ($item === null) {
            return new \StdClass;
        }
        return $marshaler->unmarshalItem($item, true);
    }

    public function unmarshalBatch($itemList)
    {
        if ($itemList === null) {
            return [];
        }
        $list = [];
        foreach ($itemList as $item) {
            $list[] = $this->unmarshalItem($item);
        }
        return $list;
    }

    public function createGetQuery($tableName, $primaryKey, $resourceId)
    {
        $query = [
            'TableName' => $tableName,
            'Key' => $this->marshalItem([$primaryKey => $resourceId]),
        ];
        return $query;
    }

    public function createScanQuery($tableName)
    {
        $query = [
            'TableName' => $tableName,
        ];
        return $query;
    }

    public function createPostQuery($tableName, $primaryKey, $newResource)
    {
        $query = [
            'TableName' => $tableName,
            'Item' => $this->marshalItem($newResource),
            'ConditionExpression' => 'attribute_not_exists(#pk)',
            "ExpressionAttributeNames" => ["#pk" => $primaryKey],
        ];
        return $query;
    }

    public function createPutQuery($tableName, $primaryKey, $newResource)
    {
        $query = [
            'TableName' => $tableName,
            'Item' => $this->marshalItem($newResource),
            'ConditionExpression' => 'attribute_exists(#pk)',
            "ExpressionAttributeNames" => ["#pk" => $primaryKey],
        ];
        return $query;
    }

    public function createDeleteQuery($tableName, $primaryKey, $resourceId)
    {
        $query = [
            'TableName' => $tableName,
            'Key' => $this->marshalItem([$primaryKey => $resourceId]),
        ];
        return $query;
    }
}
