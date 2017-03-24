<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Clusterpoint\Database;

class DatabaseTest extends TestCase
{
    public function testPut()
    {
        $database = new Database($this->mockClient, 'DATABASENAME');
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('insertOne')
            ->andReturn($this->mockResponse(['results' => [$resource]]));
        $res = $database->post('resourceName', '_id', $resource);
        $this->assertEquals('ID', $res->_id);
        $this->assertEquals('TITLE', $res->title);
    }

    public function testPost()
    {
        $database = new Database($this->mockClient, 'DATABASENAME');
        $resource = new \StdClass;
        $id = 'ID';
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('update')
            ->andReturn($this->mockResponse(['results' => [$resource]]));
        $res = $database->put('resourceName', '_id', $id, $resource);
        $this->assertEquals('ID', $res->_id);
        $this->assertEquals('TITLE', $res->title);
    }
}
