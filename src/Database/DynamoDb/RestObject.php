<?php
namespace Fathomminds\Rest\Database\DynamoDb;

use Aws\DynamoDb\Marshaler;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Objects\RestObject as CoreRestObject;
use GuzzleHttp\Promise as PromiseFunctions;
use GuzzleHttp\Promise\Promise as Promise;

class RestObject extends CoreRestObject
{
    protected $primaryKey = '_id';
    protected $databaseClass = Database::class;
    protected $indexNames = [];

    public function validateUniqueFields()
    {
        $uniqueFields = $this->getUniqueFields();
        $uniqueFields = array_diff($uniqueFields, [$this->primaryKey]); //Secondary indexes only
        if ($this->allUniqueFieldHasIndex($uniqueFields)) {
            return $this->queryUniqueFields($uniqueFields);
        }
        return $this->scanUniqueFields($uniqueFields);
    }

    protected function allUniqueFieldHasIndex($fields)
    {
        $indexes = array_keys($this->indexNames);
        $existingFields = array_keys(get_object_vars($this->resource));
        $uniqueFields = array_diff(
            array_intersect($fields, $existingFields), //Is unique and value is set
            [$this->primaryKey] //Collect only secondary indexes
        );
        $indexedFields = array_intersect($uniqueFields, $indexes); //Is unique and value is set and is indexed
        sort($uniqueFields);
        sort($indexedFields);
        return $uniqueFields===$indexedFields;
    }

    protected function scanUniqueFields($fields)
    {
        $client = $this->getClient();
        $query = new Scan($client, $this->generateScan($fields));
        while ($res = $query->next()) {
            if ($res !== null && $res['Count'] !== 0) {
                throw new RestException(
                    'Unique constraint violation',
                    [
                        'resourceName' => $this->resourceName,
                        'confilct' => $res,
                        'mode' => 'scan',
                    ]
                );
            }
        }
    }

    protected function generateScan($fields)
    {
        $filter = $this->generateScanFilter($fields);
        if (property_exists($this->resource, $this->primaryKey)) {
            $marshaler = new Marshaler;
            $filter['FilterExpression'] = '('.$filter['FilterExpression'].') AND #pk<>:pk';
            $filter['ExpressionAttributeNames']['#pk'] = $this->primaryKey;
            $filter['ExpressionAttributeValues'][':pk'] = $marshaler->marshalValue(
                $this->resource->{$this->primaryKey}
            );
        }
        return [
            'TableName' => $this->database->getDatabaseName() . '-' . $this->resourceName,
            'FilterExpression' => $filter['FilterExpression'],
            'ExpressionAttributeNames' => $filter['ExpressionAttributeNames'],
            'ExpressionAttributeValues' => $filter['ExpressionAttributeValues'],
        ];
    }

    protected function generateScanFilter($fields)
    {
        $marshaler = new Marshaler;
        $ret = [
            'FilterExpression' => '',
            'ExpressionAttributeNames' => [],
            'ExpressionAttributeValues' => [],
        ];
        foreach ($fields as $field) {
            if (property_exists($this->resource, $field)) {
                $ret['FilterExpression'] .= '#'.$field.'=:'.$field.' OR ';
                $ret['ExpressionAttributeNames']['#'.$field] = $field;
                $ret['ExpressionAttributeValues'][':'.$field] = $marshaler->marshalValue(
                    $this->resource->{$field}
                );
            }
        }
        $ret['FilterExpression'] = trim($ret['FilterExpression'], ' OR ');
        return $ret;
    }

    protected function queryUniqueFields($fields)
    {
        $queries = $this->generateQueries($fields);
        $promises = $this->generatePromises($queries);
        $results = PromiseFunctions\unwrap($promises);
        foreach ($results as $result) {
            if ($result !== null && $result['Count'] !== 0) {
                throw new RestException(
                    'Unique constraint violation',
                    [
                        'resourceName' => $this->resourceName,
                        'confilct' => $result,
                        'mode' => 'query',
                    ]
                );
            }
        }
    }

    protected function generateQueries($fields)
    {
        $queries = [];
        foreach ($fields as $field) {
            if (property_exists($this->resource, $field)) {
                $queries[] = $this->generateQuery($field);
            }
        }
        return $queries;
    }

    protected function generateQuery($field)
    {
        $marshaler = new Marshaler;
        $query = [
            'TableName' => $this->database->getDatabaseName() . '-' . $this->resourceName,
            'KeyConditionExpression' => '#'.$field.'=:'.$field,
            'IndexName' => $this->indexNames[$field],
            'ExpressionAttributeNames' => ['#'.$field => $field],
            'ExpressionAttributeValues' => [':'.$field =>  $marshaler->marshalValue(
                $this->resource->{$field}
            )],
        ];
        if (property_exists($this->resource, $this->primaryKey)) {
            $query['FilterExpression'] = '#pk<>:pk';
            $query['ExpressionAttributeNames']['#pk'] = $this->primaryKey;
            $query['ExpressionAttributeValues'][':pk'] = $marshaler->marshalValue(
                $this->resource->{$this->primaryKey}
            );
        }
        return $query;
    }

    protected function generatePromises($queries)
    {
        $client = $this->getClient();
        $promises = [];
        foreach ($queries as $query) {
            $promises[] = $this->generatePromise($client, $query);
        }
        return $promises;
    }

    protected function generatePromise($client, $query)
    {
        $promise = new Promise(
            function () use (&$promise, $client, $query) {
                $q = new Query($client, $query);
                while ($res = $q->next()) {
                    if ($res['Count'] !== 0) {
                        $promise->resolve($res);
                        return;
                    }
                }
                $promise->resolve(null);
            }
        );
        return $promise;
    }
}
