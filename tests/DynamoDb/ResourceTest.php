<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\DynamoDb\Resource;
use Aws\DynamoDb\DynamoDbClient;

class ResourceTest extends TestCase
{
    public function testPut()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $id = 'ID';
        $resource = new \StdClass();
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andReturn($resource);
        $rest = new Resource('dummy', '_id', $client, 'DBNAME');
        try {
            $ret = $rest->put($id, $resource);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testPutException()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $id = 'ID';
        $resource = new \StdClass();
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andThrow(RestException::class, 'Error');
        $rest = new Resource('dummy', '_id', $client, 'DBNAME');
        try {
            $ret = $rest->put($id, $resource);
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertEquals('Error', $ex->getMessage());
        }
    }

    public function testPost()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $id = 'ID';
        $resource = new \StdClass();
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andReturn($resource);
        $rest = new Resource('dummy', '_id', $client, 'DBNAME');
        try {
            $ret = $rest->post($resource);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testPostException()
    {
        $client = Mockery::mock(DynamoDbClient::class);
        $id = 'ID';
        $resource = new \StdClass();
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $client
            ->shouldReceive('putItem')
            ->andThrow(RestException::class, 'Error');
        $rest = new Resource('dummy', '_id', $client, 'DBNAME');
        try {
            $ret = $rest->post($resource);
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertEquals('Error', $ex->getMessage());
        }
    }
}
