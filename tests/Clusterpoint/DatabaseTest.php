<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Clusterpoint\Database;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FooModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;

class DatabaseTest extends TestCase
{
    public function testPut()
    {
        $database = new Database($this->mockClient, 'DATABASENAME');
        $resource = new FooSchema;
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
        $resource = new FooSchema;
        $id = 'ID';
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('replace')
            ->andReturn($this->mockResponse(['results' => [$resource]]));
        $res = $database->put('resourceName', '_id', $id, $resource);
        $this->assertEquals('ID', $res->_id);
        $this->assertEquals('TITLE', $res->title);
    }

    public function testPatch()
    {
        $database = new Database($this->mockClient, 'DATABASENAME');
        $resource = new FooSchema;
        $id = 'ID';
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('update')
            ->andReturn($this->mockResponse(['results' => [$resource]]));
        $res = $database->patch('resourceName', '_id', $id, $resource);
        $this->assertEquals('ID', $res->_id);
        $this->assertEquals('TITLE', $res->title);
    }

    public function testSetDatabaseName()
    {
        $testDatabaseName = 'TestDatabaseName';
        $database = new Database($this->mockClient, 'DATABASENAME');
        $database->setDatabaseName($testDatabaseName);
        $this->assertEquals($database->getDatabaseName(), $testDatabaseName);
    }
}
