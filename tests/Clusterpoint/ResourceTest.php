<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Clusterpoint\Resource;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;

class ResourceTest extends TestCase
{
    public function testPut()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('update')
            ->andReturn($this->mockResponse($resource));
        $rest = new Resource('dummy', '_id', $this->mockClient, 'DBNAME');
        try {
            $ret = $rest->put($id, $resource);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testPutException()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('update')
            ->andThrow(RestException::class, 'Error');
        $rest = new Resource('dummy', '_id', $this->mockClient, 'DBNAME');
        try {
            $ret = $rest->put($id, $resource);
            $this->fail();
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->assertEquals('Error', $ex->getMessage());
        }
    }

    public function testPost()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('insertOne')
            ->andReturn($this->mockResponse($resource));
        $rest = new Resource('dummy', '_id', $this->mockClient, 'DBNAME');
        try {
            $ret = $rest->post($resource);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testPostException()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('insertOne')
            ->andThrow(RestException::class, 'Error');
        $rest = new Resource('dummy', '_id', $this->mockClient, 'DBNAME');
        try {
            $ret = $rest->post($resource);
            $this->fail();
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->assertEquals('Error', $ex->getMessage());
        }
    }

    public function testFailOnErrorResourceDoesNotExist()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $error = new \StdClass;
        $error->message = 'Requested document does not exist';
        $this->mockDatabase
            ->shouldReceive('find')
            ->andReturn($this->mockResponse(
                [],
                [$error]
            ));
        $rest = new Resource('dummy', '_id', $this->mockClient, 'DBNAME');
        try {
            $ret = $rest->get($id);
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertEquals('Resource does not exist', $ex->getMessage());
        }
    }

    public function testFailOnErrorDataaseOperationFailed()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $error = new \StdClass;
        $error->message = 'Some Clusterpoint Database error message';
        $this->mockDatabase
            ->shouldReceive('find')
            ->andReturn($this->mockResponse(
                [],
                [$error]
            ));
        $rest = new Resource('dummy', '_id', $this->mockClient, 'DBNAME');
        try {
            $ret = $rest->get($id);
            $this->fail();
        } catch (\Exception $ex) {
            $this->assertEquals('Database operation failed', $ex->getMessage());
        }
    }
}
