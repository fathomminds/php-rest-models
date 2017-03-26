<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\DynamoDb\Query;
use Fathomminds\Rest\Database\DynamoDb\Scan;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Marshaler;
use Aws\Sdk;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FooSchema;

class DatabaseOperationTest extends TestCase
{
    public function testQuery()
    {
        $marshaler = new Marshaler;
        $client = Mockery::mock(DynamoDbClient::class);
        $request = [
            'TableName' => 'TABLENAME',
            'KeyConditionExpression' => '#field=:field',
            'ExpressionAttributeNames' => ['#field' => 'fieldName'],
            'ExpressionAttributeValues' => [':field' => $marshaler->marshalValue('VALUE')],
        ];
        $query = new Query($client, $request);

        $property = new \ReflectionProperty($query, 'query');
        $property->setAccessible(true);
        $value = $property->getValue($query);
        $this->assertEquals($request, $value);

        $property = new \ReflectionProperty($query, 'client');
        $property->setAccessible(true);
        $value = $property->getValue($query);
        $this->assertEquals($client, $value);
    }

    public function testNextWithScan()
    {
        $marshaler = new Marshaler;
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new FooSchema;
        $item->_id = 'ID';
        $item->title = 'TITLE';
        $list = [$item];
        $client
            ->shouldReceive('scan')
            ->andReturn($this->mockBatchResponse(['Items'=>$list]));
        $request = [
            'TableName' => 'TABLENAME',
            'FilterExpression' => '#field=:field',
            'ExpressionAttributeNames' => ['#field' => 'fieldName'],
            'ExpressionAttributeValues' => [':field' => 'fieldValue'],
        ];
        $query = new Scan($client, $request);
        $items = [];
        while ($res = $query->next()) {
            foreach ($res['Items'] as $item) {
                $items[] = $marshaler->unmarshalItem($item, true);
            }
        }
        $item = $items[0];
        $this->assertEquals('ID', $item->_id);
        $this->assertEquals('TITLE', $item->title);
    }

    public function testNextWithQuery()
    {
        $marshaler = new Marshaler;
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new FooSchema;
        $item->_id = 'ID';
        $item->title = 'TITLE';
        $list = [$item];
        $client
            ->shouldReceive('query')
            ->andReturn($this->mockBatchResponse(['Items'=>$list]));
        $request = [
            'TableName' => 'cta-dev-1-todo_items',
            'KeyConditionExpression' => '#field=:field',
            'ExpressionAttributeNames' => ['#field' => '_id'],
            'ExpressionAttributeValues' => [
                ':field' => $marshaler->marshalValue('e8dfd22d-26c2-4d3f-9989-c187073c370a')
            ],
        ];
        $query = new Query($client, $request);
        $items = [];
        while ($res = $query->next()) {
            foreach ($res['Items'] as $item) {
                $items[] = $marshaler->unmarshalItem($item, true);
            }
        }
        $item = $items[0];
        $this->assertEquals('ID', $item->_id);
        $this->assertEquals('TITLE', $item->title);
    }

    public function testNextWithQueryMultiPage()
    {
        $marshaler = new Marshaler;
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new FooSchema;
        $item->_id = 'ID';
        $item->title = 'TITLE';
        $list = [$item];
        $client
            ->shouldReceive('query')
            ->once()
            ->andReturn($this->mockBatchResponse([
                'Items' => [],
                'LastEvaluatedKey' => $marshaler->marshalValue($item->_id),
            ]));
        $client
            ->shouldReceive('query')
            ->once()
            ->andReturn($this->mockBatchResponse([
                'Items' => $list,
            ]));
        $request = [
            'TableName' => 'cta-dev-1-todo_items',
            'KeyConditionExpression' => '#field=:field',
            'ExpressionAttributeNames' => ['#field' => '_id'],
            'ExpressionAttributeValues' => [
                ':field' => $marshaler->marshalValue('e8dfd22d-26c2-4d3f-9989-c187073c370a')
            ],
        ];
        $query = new Query($client, $request);
        $items = [];
        while ($res = $query->next()) {
            foreach ($res['Items'] as $item) {
                $items[] = $marshaler->unmarshalItem($item, true);
            }
        }
        $item = $items[0];
        $this->assertEquals('ID', $item->_id);
        $this->assertEquals('TITLE', $item->title);
    }
}
