<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use Mockery;
use Aws\DynamoDb\DynamoDbClient;
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
use Aws\Command;
use Fathomminds\Rest\Database\DynamoDb\Database;
use Fathomminds\Rest\Database\DynamoDb\Resource;
use Fathomminds\Rest\Examples\DynamoDb\Models\Objects\FooObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\FooModel;
use Fathomminds\Rest\Exceptions\RestException;

class DynamoDbTest extends TestCase
{
    public function testConstruct()
    {
        $database = new Database;
        $databaseName = getenv('AWS_DYNAMODB_NAMESPACE') . '-' . getenv('AWS_DYNAMODB_DATABASE');
        $client = $database->getClient();
        $this->assertEquals($databaseName, $database->getDatabaseName());
        $this->assertEquals(get_class($client), DynamoDbClient::class);
    }

    public function testDatabaseGet()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->_id = 'ID';
        $item->title = 'TITLE';
        $client
            ->shouldReceive('getItem')
            ->andReturn($this->mockResponse($item));
        $database = new Database($client, 'DATABSENAME');
        $res = $database->get('resourcename', 'primarykey', 'id');
        $this->assertEquals('ID', $res->_id);
        $this->assertEquals('TITLE', $res->title);
    }

    public function testDatabasePost()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andReturn('anything');
        $database = new Database($client, 'DATABSENAME');
        $res = $database->post('resourcename', '_id', $item);
        $this->assertEquals('TITLE', $res->title);
        $this->assertEquals(
            1,
            preg_match('/^[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}$/', $res->_id)
        );
    }

    public function testDatabasePut()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->_id = 'ANYID';
        $item->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andReturn('anything');
        $database = new Database($client, 'DATABSENAME');
        $res = $database->put('resourcename', '_id', 'UPDATE_THIS', $item);
        $this->assertEquals('TITLE', $res->title);
        $this->assertEquals('UPDATE_THIS', $res->_id);
    }

    public function testDatabasePutException()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $command = Mockery::mock(Command::class, ['CommandName', []]);
        $ex = Mockery::mock(DynamoDbException::class, ['SomeAwsErrorMessage', $command]);
        $item = new \StdClass;
        $item->_id = 'ANYID';
        $item->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andThrow($ex);
        $database = new Database($client, 'DATABSENAME');
        try {
            $res = $database->put('resourcename', '_id', 'UPDATE_THIS', $item);
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('SomeAwsErrorMessage', $ex->getMessage());
        }
    }

    public function testDatabaseDelete()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->_id = 'ANYID';
        $item->title = 'TITLE';
        $client
            ->shouldReceive('deleteItem')
            ->andReturn('anything');
        $database = new Database($client, 'DATABSENAME');
        $res = $database->delete('resourcename', '_id', 'DELETE_THIS');
        $this->assertEquals('DELETE_THIS', $res);
    }

    public function testDatabaseDeleteException()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $command = Mockery::mock(Command::class, ['CommandName', []]);
        $ex = Mockery::mock(DynamoDbException::class, ['SomeAwsErrorMessage', $command]);
        $client
            ->shouldReceive('deleteItem')
            ->andThrow($ex);
        $database = new Database($client, 'DATABSENAME');
        try {
            $res = $database->delete('resourcename', '_id', 'ID');
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('SomeAwsErrorMessage', $ex->getMessage());
        }
    }

    public function testResourceConstruct()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $resource = new Resource('resourceName', 'primaryKey', $client, 'databaseName');
        $class = new \ReflectionObject($resource);

        $property = $class->getProperty('client');
        $property->setAccessible(true);
        $value = $property->getValue($resource);
        $this->assertEquals(get_class($value), get_class($client));

        $property = $class->getProperty('resourceName');
        $property->setAccessible(true);
        $value = $property->getValue($resource);
        $this->assertEquals('resourceName', $value);

        $property = $class->getProperty('primaryKey');
        $property->setAccessible(true);
        $value = $property->getValue($resource);
        $this->assertEquals('primaryKey', $value);

        $property = $class->getProperty('databaseName');
        $property->setAccessible(true);
        $value = $property->getValue($resource);
        $this->assertEquals('databaseName', $value);
    }

    public function testResourceCreateClient()
    {
        $resource = new Resource('resourceName', 'primaryKey');
        $class = new \ReflectionObject($resource);

        $method = $class->getMethod('createClient');
        $method->setAccessible(true);
        $value = $method->invokeArgs($resource, []);
        $this->assertEquals(DynamoDbClient::class, get_class($value));
    }

    public function testResourceGetAll()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->_id = 'ID';
        $item->title = 'TITLE';
        $list = [$item];
        $client
            ->shouldReceive('scan')
            ->andReturn($this->mockBatchResponse($list));
        $database = new Database($client, 'DATABSENAME');
        $res = $database->get('resourcename', 'primarykey');
        $this->assertCount(1, $res);
        $this->assertEquals('ID', $res[0]->_id);
        $this->assertEquals('TITLE', $res[0]->title);
    }

    public function testDatabasePostPrimaryKeyCollisionUnresolved()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->title = 'TITLE';
        $ex = Mockery::mock(DynamoDbException::class);
        $ex
            ->shouldReceive('getAwsErrorCode')
            ->andReturn('ConditionalCheckFailedException');
        $client
            ->shouldReceive('putItem')
            ->andThrow($ex);
        $database = new Database($client, 'DATABSENAME');
        try {
            $res = $database->post('resourcename', '_id', $item);
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Creating new resource failed', $ex->getMessage());
            $this->assertEquals(5, $ex->getDetails()['retryCount']);
        }
    }

    public function testDatabasePostPrimaryKeyCollisionResolved()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->title = 'TITLE';
        $ex = Mockery::mock(DynamoDbException::class);
        $ex
            ->shouldReceive('getAwsErrorCode')
            ->andReturn('ConditionalCheckFailedException');
        $client
            ->shouldReceive('putItem')
            ->once()
            ->andThrow($ex);
        $client
            ->shouldReceive('putItem')
            ->once()
            ->andReturn('anything');
        $database = new Database($client, 'DATABSENAME');
        $res = $database->post('resourcename', '_id', $item);
        $this->assertEquals('TITLE', $res->title);
        $this->assertEquals(
            1,
            preg_match('/^[0-9a-z]{8}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{4}-[0-9a-z]{12}$/', $res->_id)
        );
    }

    public function testDatabasePostOtherAwsException()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->title = 'TITLE';
        $command = Mockery::mock(Command::class, ['CommandName', []]);
        $ex = Mockery::mock(DynamoDbException::class, ['SomeAwsErrorMessage', $command]);
        $ex
            ->shouldReceive('getAwsErrorCode')
            ->andReturn('SomeAwsErrorCode');
        $client
            ->shouldReceive('putItem')
            ->andThrow($ex);
        $database = new Database($client, 'DATABSENAME');
        try {
            $res = $database->post('resourcename', '_id', $item);
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('SomeAwsErrorMessage', $ex->getMessage());
            $this->assertEquals(0, $ex->getDetails()['retryCount']);
        }
    }

    public function testDatabasePostException()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->title = 'TITLE';
        $ex = new \Exception('SomeException');
        $client
            ->shouldReceive('putItem')
            ->andThrow($ex);
        $database = new Database($client, 'DATABSENAME');
        try {
            $res = $database->post('resourcename', '_id', $item);
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('SomeException', $ex->getMessage());
            $this->assertEquals(0, $ex->getDetails()['retryCount']);
        }
    }

    public function testDatabaseUnmarshalItemNull()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $resource = new Resource('resourceName', 'primaryKey', $client, 'databaseName');
        $marshaler = new Marshaler;
        $class = new \ReflectionObject($resource);
        $method = $class->getMethod('unmarshalItem');
        $method->setAccessible(true);
        $unmarshaledItem = $method->invokeArgs($resource, [null]);
        $this->assertEquals('object', gettype($unmarshaledItem));
        $this->assertEquals('stdClass', get_class($unmarshaledItem));
        $this->assertCount(0, get_object_vars($unmarshaledItem));
    }

    public function testDatabaseUnmarshalBatchNull()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $resource = new Resource('resourceName', 'primaryKey', $client, 'databaseName');
        $marshaler = new Marshaler;
        $class = new \ReflectionObject($resource);
        $method = $class->getMethod('unmarshalBatch');
        $method->setAccessible(true);
        $unmarshaledItem = $method->invokeArgs($resource, [null]);
        $this->assertEquals('array', gettype($unmarshaledItem));
        $this->assertCount(0, $unmarshaledItem);
    }

    public function testValidateUniqueFields()
    {
        $resource = new \StdClass;
        $resource->_id = '2c5e9a27-68bd-40cf-adf2-2e8eb022a192';
        $resource->title = 'TITLE';
        //$resource->other = 'OTHER';
        //$resource->noindex = 'noindex';

        $object = new FooObject($resource);
        try {
            $object->validateUniqueFields();
            $this->assertTrue(true);
        } catch (RestException $ex) {
            var_dump($ex->getMessage());
            var_dump($ex->getDetails());
            $this->fail();
        }
    }
}
