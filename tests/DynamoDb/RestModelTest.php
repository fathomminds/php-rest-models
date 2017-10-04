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
use Fathomminds\Rest\Helpers\Uuid;

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

    public function testUseIncorrectType()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $database = Mockery::mock(Database::class);
        $object = new FooObject(null, null, $database);
        $property = new \ReflectionProperty($object, 'schemaClass');
        $property->setAccessible(true);
        $property->setValue($object, 'noSuchClass');
        $model = new FooModel($object);
        try {
            $model->resource($resource);
            $this->fail();
        } catch (RestException $ex) {
            $this->assertEquals('Setting model resource failed', $ex->getMessage());
        }
    }

    public function testDatabaseGet()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->_id = 'ID';
        $item->title = 'TITLE';
        $client
            ->shouldReceive('getItem')
            ->andReturn($this->mockResponse(['Item' => $item]));
        $database = new Database($client, 'DATABSENAME');
        $res = $database->get('resourcename', 'primarykey', 'id');
        $this->assertEquals('ID', $res->_id);
        $this->assertEquals('TITLE', $res->title);
    }

    public function testDatabasePost()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $item = new \StdClass;
        $item->_id = (new Uuid)->generate();
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
        $res = $database->put('resourcename', '_id', 'REPLACE_THIS', $item);
        $this->assertEquals('TITLE', $res->title);
        $this->assertEquals('REPLACE_THIS', $res->_id);
    }

    public function testDatabasePutException()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $command = Mockery::mock(Command::class, ['CommandName', []]);
        $ex = Mockery::mock(DynamoDbException::class, ['SomeAwsErrorMessage', $command]);
        $ex
            ->shouldReceive('getAwsErrorCode')
            ->andReturn('ConditionalCheckFailedException');
        $item = new \StdClass;
        $item->_id = 'ANYID';
        $item->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andThrow($ex);
        $database = new Database($client, 'DATABSENAME');
        try {
            $res = $database->put('resourcename', '_id', 'REPLACE_THIS', $item);
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Resource does not exist', $ex->getMessage());
        }
    }

    public function testDatabasePutExceptionOtherAwsError()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $command = Mockery::mock(Command::class, ['CommandName', []]);
        $ex = Mockery::mock(DynamoDbException::class, ['SomeAwsErrorMessage', $command]);
        $ex
            ->shouldReceive('getAwsErrorCode')
            ->andReturn('OtherAwsErrorCode');
        $item = new \StdClass;
        $item->_id = 'ANYID';
        $item->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andThrow($ex);
        $database = new Database($client, 'DATABSENAME');
        try {
            $res = $database->put('resourcename', '_id', 'REPLACE_THIS', $item);
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
            ->andReturn($this->mockBatchResponse(['Items' => $list]));
        $database = new Database($client, 'DATABSENAME');
        $res = $database->get('resourcename', 'primarykey');
        $this->assertCount(1, $res);
        $this->assertEquals('ID', $res[0]->_id);
        $this->assertEquals('TITLE', $res[0]->title);
    }

    public function testDatabasePostPrimaryKeyCollision()
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
            $this->assertEquals('Primary key collision', $ex->getMessage());
        }
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
        }
    }

    public function testQuery()
    {
        $model = new FooModel;
        $q = $model->query();
        $this->assertNull($q);
    }
}
