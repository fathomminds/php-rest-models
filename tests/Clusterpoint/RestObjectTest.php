<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Clusterpoint\Database;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\NoUniqueFieldObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\NoUniqueFieldSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FooObject;
use Fathomminds\Rest\Database\Clusterpoint\Finder;

class RestObjectTest extends TestCase
{
    public function testNoUniqueFieldSchemaValidation()
    {
        $object = $this->mockObject(NoUniqueFieldObject::class);
        $input = new NoUniqueFieldSchema;
        $input->_id = 'ID';
        $input->multi = 'MULTI';
        $object = $object->createFromObject($input);
        $object->validate(); // Trigger early return in RestObject::validateUniqueFields
        $uniqueFields = $object->getUniqueFields();
        $this->assertCount(0, $uniqueFields);
    }

    public function testPut()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $schema = Mockery::mock(FooSchema::class);
        $schema
            ->shouldReceive('allowExtraneous')
            ->andReturn(null);
        $schema
            ->shouldReceive('getFields')
            ->andReturn([]);
        $schema
            ->shouldReceive('getUniqueFields')
            ->andReturn([]);
        $schema
            ->shouldReceive('validate')
            ->andReturn(null);
        $database = Mockery::mock(Database::class);
        $database
            ->shouldReceive('put')
            ->andReturn($resource);
        $object = new FooObject($resource, $schema, $database);
        try {
            $object = $object->put($id, $resource);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testPatch()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $schema = Mockery::mock(FooSchema::class);
        $schema
            ->shouldReceive('allowExtraneous')
            ->andReturn(null);
        $schema
            ->shouldReceive('getFields')
            ->andReturn([]);
        $schema
            ->shouldReceive('getUniqueFields')
            ->andReturn([]);
        $schema
            ->shouldReceive('validate')
            ->andReturn(null);
        $database = Mockery::mock(Database::class);
        $database
            ->shouldReceive('patch')
            ->andReturn($resource);
        $object = new FooObject($resource, $schema, $database);
        try {
            $object = $object->patch($id, $resource);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testPost()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $schema = Mockery::mock(FooSchema::class);
        $schema
            ->shouldReceive('allowExtraneous')
            ->andReturn(null);
        $schema
            ->shouldReceive('getFields')
            ->andReturn([]);
        $schema
            ->shouldReceive('getUniqueFields')
            ->andReturn([]);
        $schema
            ->shouldReceive('validate')
            ->andReturn(null);
        $database = Mockery::mock(Database::class);
        $database
            ->shouldReceive('post')
            ->andReturn($resource);
        $object = new FooObject($resource, $schema, $database);
        try {
            $object = $object->post($resource);
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testSetFieldDefaultsSkipExisting()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $database = Mockery::mock(Database::class);
        $object = new FooObject($resource, null, $database);
        try {
            $method = new \ReflectionMethod($object, 'setFieldDefaults');
            $method->setAccessible(true);
            $method->invoke($object);
            $res = $object->resource();
            $this->assertEquals('ID', $res->_id);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testValidateUniqueFieldsReplaceMode()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $database = new Database($this->mockClient, 'DatabaseName');
        $this->mockDatabase
            ->shouldReceive('where')
            ->andReturn($this->mockDatabase);
        $this->mockDatabase
            ->shouldReceive('limit')
            ->andReturn($this->mockDatabase);
        $mockResponse = $this->mockResponse($resource);
        $mockResponse
            ->shouldReceive('hits')
            ->andReturn(0);
        $this->mockDatabase
            ->shouldReceive('get')
            ->andReturn($mockResponse);
        $object = new FooObject($resource, null, $database);
        $object->replaceMode(true);
        try {
            $res = $object->validate();
            $this->assertTrue(true); //Should reach this line
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testValidateUniqueFieldsUpdateMode()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $database = new Database($this->mockClient, 'DatabaseName');
        $this->mockDatabase
            ->shouldReceive('where')
            ->andReturn($this->mockDatabase);
        $this->mockDatabase
            ->shouldReceive('limit')
            ->andReturn($this->mockDatabase);
        $mockResponse = $this->mockResponse($resource);
        $mockResponse
            ->shouldReceive('hits')
            ->andReturn(0);
        $this->mockDatabase
            ->shouldReceive('get')
            ->andReturn($mockResponse);
        $object = new FooObject($resource, null, $database);
        $object->updateMode(true);
        try {
            $res = $object->validate();
            $this->assertTrue(true); //Should reach this line
        } catch (\Exception $ex) {
            $this->fail();
        }
    }

    public function testValidateUniqueFieldsPrimaryKeyCollision()
    {
        $id = 'ID';
        $resource = new FooSchema;
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $database = new Database($this->mockClient, 'DatabaseName');
        $this->mockDatabase
            ->shouldReceive('where')
            ->andReturn($this->mockDatabase);
        $this->mockDatabase
            ->shouldReceive('limit')
            ->andReturn($this->mockDatabase);
        $mockResponse = $this->mockResponse(['results' => [$resource]]);
        $mockResponse
            ->shouldReceive('hits')
            ->andReturn(1);
        $this->mockDatabase
            ->shouldReceive('get')
            ->andReturn($mockResponse);
        $object = new FooObject($resource, null, $database);
        try {
            $res = $object->validateUniqueFields();
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Primary key collision', $ex->getMessage());
        }
    }

    public function testFind()
    {
        $database = new Database($this->mockClient, 'DatabaseName');
        $object = new FooObject(null, null, $database);
        $finder = $object->find();
        $this->assertEquals(Finder::class, get_class($finder));
    }

    public function testFindWithMockclient()
    {
        $database = new Database($this->mockClient, 'DatabaseName');
        $object = new FooObject(null, null, $database);
        $finder = $object->find($this->mockClient);
        $this->assertEquals(Finder::class, get_class($finder));
    }

    public function testSetDatabaseName()
    {
        $testDatabaseName = 'TestDatabaseName';
        $mockDatabase = Mockery::mock(Database::class);
        $mockDatabase->shouldReceive('setDatabaseName')->with($testDatabaseName)->once();
        $object = new FooObject(null, null, $mockDatabase);
        $object->setDatabaseName($testDatabaseName);
        $this->assertEquals(1, 1); // Must reach this line
    }

    public function testGetDatabaseName()
    {
        $mockDatabase = Mockery::mock(Database::class);
        $mockDatabase->shouldReceive('getDatabaseName')->withNoArgs()->once();
        $object = new FooObject(null, null, $mockDatabase);
        $object->getDatabaseName();
        $this->assertEquals(1, 1); // Must reach this line
    }
}
