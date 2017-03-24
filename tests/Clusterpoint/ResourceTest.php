<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Clusterpoint\Resource;

class ResourceTest extends TestCase
{
    public function testPut()
    {
        $id = 'ID';
        $resource = new \StdClass();
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
        $resource = new \StdClass();
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
        $resource = new \StdClass();
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
        $resource = new \StdClass();
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
}
