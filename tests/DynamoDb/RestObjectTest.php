<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Examples\DynamoDb\Models\Objects\FooObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FooSchema;
use Fathomminds\Rest\Database\DynamoDb\Database;
use Aws\DynamoDb\DynamoDbClient;

class RestObjectTest extends TestCase
{
    public function testValidateUniqueFieldsWithQuery()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $client = Mockery::mock(DynamoDbClient::class);
        $database = new Database($client, 'DATABASENAME');
        $client
            ->shouldReceive('query')
            ->andReturn($this->mockBatchResponse([
                'Count' => 0,
            ]));
        $object = new FooObject($resource, null, $database);
        try {
            $object->validateUniqueFields();
            $this->assertTrue(true); //Must reach this line
        } catch (RestException $ex) {
            $this->fail(); //Should not thorw exception
        }
    }

    public function testValidateUniqueFieldsWithScan()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $client = Mockery::mock(DynamoDbClient::class);
        $database = new Database($client, 'DATABASENAME');
        $client
            ->shouldReceive('scan')
            ->andReturn($this->mockBatchResponse([
                'Count' => 0,
            ]));
        $object = new FooObject($resource, null, $database);

        $property = new \ReflectionProperty($object, 'indexNames');
        $property->setAccessible(true);
        $property->setValue($object, []);

        try {
            $object->validateUniqueFields();
            $this->assertTrue(true); //Must reach this line
        } catch (RestException $ex) {
            $this->fail(); //Should not thorw exception
        }
    }

    public function testValidateUniqueFieldsWithQueryException()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $client = Mockery::mock(DynamoDbClient::class);
        $database = new Database($client, 'DATABASENAME');
        $client
            ->shouldReceive('query')
            ->andReturn($this->mockBatchResponse([
                'Count' => 1,
            ]));
        $object = new FooObject($resource, null, $database);
        try {
            $object->validateUniqueFields();
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Unique constraint violation', $ex->getMessage());
        }
    }

    public function testValidateUniqueFieldsWithScanException()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $client = Mockery::mock(DynamoDbClient::class);
        $database = new Database($client, 'DATABASENAME');
        $client
            ->shouldReceive('scan')
            ->andReturn($this->mockBatchResponse([
                'Count' => 1,
            ]));
        $object = new FooObject($resource, null, $database);

        $property = new \ReflectionProperty($object, 'indexNames');
        $property->setAccessible(true);
        $property->setValue($object, []);

        try {
            $object->validateUniqueFields();
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Unique constraint violation', $ex->getMessage());
        }
    }

    public function testValidateUniqueFieldsEmptySchema()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $client = Mockery::mock(DynamoDbClient::class);
        $database = new Database($client, 'DATABASENAME');
        $schema = Mockery::mock(FooSchema::class);
        $schema
            ->shouldReceive('getUniqueFields')
            ->andReturn([]);
        $object = new FooObject($resource, $schema, $database);
        try {
            $object->validateUniqueFields();
            $this->assertTrue(true);
        } catch (RestException $ex) {
            $this->fail();
        }
    }
}
