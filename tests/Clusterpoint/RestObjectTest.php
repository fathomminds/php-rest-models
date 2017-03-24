<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Fathomminds\Rest\Database\Clusterpoint\Database;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\NoUniqueFieldObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FooObject;

class RestObjectTest extends TestCase
{
    public function testNoUniqueFieldSchemaValidation()
    {
        $object = $this->mockObject(NoUniqueFieldObject::class);
        $input = new \StdClass();
        $input->_id = 'ID';
        $input->multi = 'MULTI';
        $object = $object->createFromObject($input);
        $object->validateUniqueFields(); // Trigger early return in RestObject::validateUniqueFields
        $this->assertCount(0, $object->getUniqueFields());
    }

    public function testPut()
    {
        $id = 'ID';
        $resource = new \StdClass();
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $schema = Mockery::mock(FooSchema::class);
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

    public function testPost()
    {
        $id = 'ID';
        $resource = new \StdClass();
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $schema = Mockery::mock(FooSchema::class);
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
        $resource = new \StdClass();
        $resource->_id = $id;
        $resource->title = 'TITLE';
        $database = Mockery::mock(Database::class);
        $object = new FooObject($resource, null, $database);
        try {
            $method = new \ReflectionMethod($object, 'setFieldDefaults');
            $method->setAccessible(true);
            $method->invoke($object);
            $res = $object->getResource();
            $this->assertEquals('ID', $res->_id);
        } catch (\Exception $ex) {
            $this->fail();
        }
    }
}
