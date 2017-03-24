<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FooObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FooModel;
use Fathomminds\Rest\Exceptions\RestException;

class RestModelTest extends TestCase
{
    public function testConstructWithoutParameters()
    {
        $foo = new FooModel;
        $class = new \ReflectionClass($foo);
        $property = $class->getProperty('restObject');
        $property->setAccessible(true);
        $restObject = $property->getValue($foo);
        $className = get_class($restObject);
        $this->assertEquals(FooObject::class, $className);
    }

    public function testCreateFromObject()
    {
        $foo = $this->mockModel(FooModel::class, FooObject::class);
        $obj = new \StdClass;
        $obj->_id = 'ID';
        $obj->title = 'TITLE';
        $foo->createFromObject($obj);
        $this->assertEquals('ID', $foo->getProperty('_id'));
        $this->assertEquals('TITLE', $foo->getProperty('title'));
    }

    public function testGetResource()
    {
        $foo = $this->mockModel(FooModel::class, FooObject::class);
        $obj = new \StdClass;
        $obj->_id = 'ID';
        $obj->title = 'TITLE';
        $foo->createFromObject($obj);
        $this->assertEquals($obj, $foo->getResource());
    }

    public function testOneResourceDoesNotExtist()
    {
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('find')
            ->once()
            ->andReturn($this->mockResponse($dbResult));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $this->expectException(RestException::class);
        $model->one('ID');
    }

    public function testOneResourceIncorrectDBResponse()
    {
        $dbResult = [
            'noResultsKey' => []
        ];
        $this->mockDatabase
            ->shouldReceive('find')
            ->once()
            ->andReturn($this->mockResponse($dbResult));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $this->expectException(RestException::class);
        $model->one('ID');
    }

    public function testOneResourceExtists()
    {
        $resource = new \StdClass();
        $resource->_id = 'ID';
        $dbResult = [
            'results' => [
                $resource,
            ]
        ];
        $this->mockDatabase
            ->shouldReceive('find')
            ->once()
            ->andReturn($this->mockResponse($dbResult));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->one('ID');
        $this->assertEquals('ID', $model->getProperty('_id'));
    }

    public function testAll()
    {
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('get')
            ->once()
            ->andReturn($this->mockResponse($dbResult));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $list = $model->all();
        $this->assertEquals('array', gettype($list));
    }

    public function testCreateInvalidStructure()
    {
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('get')
            ->once()
            ->andReturn($this->mockResponse($dbResult));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $this->expectException(RestException::class);
        $list = $model->create();
    }

    public function testUpdateValidStructure()
    {
        $resource = new \StdClass;
        $resource->_id = 'NEW';
        $resource->title = 'TITLE';
        $resource->status = 0;
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('put')
            ->andReturn(null);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->createFromObject($resource);
        try {
            $model->update();
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testCreateValidStructure()
    {
        $resource = new \StdClass;
        $resource->_id = 'NEW';
        $resource->title = 'TITLE';
        $resource->status = 0;
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('post')
            ->andReturn(null);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->createFromObject($resource);
        try {
            $model->create();
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testDeleteNonExisting()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('delete')
            ->once()
            ->andReturn($this->mockResponse($dbResult, ['Assume DB failure']));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        $this->expectException(RestException::class);
        $deletedId = $model->delete();
    }

    public function testDeleteExisting()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('delete')
            ->once()
            ->andReturn($this->mockResponse($dbResult));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        $deletedId = $model->delete();
        $this->assertEquals('ID', $deletedId);
    }

    public function testValidateInvalid()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        try {
            $model->validate();
            $this->fail(); //Should throw exception
        } catch (RestException $ex) {
            $this->assertEquals('Invalid structure', $ex->getMessage());
        }
    }

    public function testValidateValid()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $mockObject = $this->mockObjectValidationOk($resource);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->createFromObject($resource);
        try {
            $model->validate();
            $this->assertTrue(true); //Should reach this line
        } catch (RestException $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testSetProperty()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        $model->setProperty('title', 'OTHER');
        $this->assertEquals('OTHER', $model->getProperty('title'));
    }

    public function testGettingNonExistentProperty()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        $prop = $model->getProperty('NO');
        $this->assertEquals(null, $prop);
    }

    public function testToArray()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        $array = $model->toArray();
        $this->assertArrayHasKey('_id', $array);
        $this->assertEquals('ID', $array['_id']);
        $this->assertArrayHasKey('title', $array);
        $this->assertEquals('TITLE', $array['title']);
    }

    public function testValidateUniqueFieldsNoConflict()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('where')
            ->andReturn($this->mockDatabase);
        $this->mockDatabase
            ->shouldReceive('limit')
            ->andReturn($this->mockDatabase);
        $dbResult = [
            'results' => []
        ];
        $mockResponse = $this->mockBatchResponse($dbResult);
        $mockResponse
            ->shouldReceive('hits')
            ->once()
            ->andReturn('0'); //Clusterpoint returns number as string
        $this->mockDatabase
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockResponse);
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        $model->validateUniqueFields();
        $this->assertEquals(1, 1); //Reaching this line if no exception is thrown
    }

    public function testValidateUniqueFieldsConflict()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $conflict = new \StdClass;
        $conflict->_id = 'CONFLICT';
        $conflict->title = 'TITLE';
        $this->mockDatabase
            ->shouldReceive('where')
            ->andReturn($this->mockDatabase);
        $this->mockDatabase
            ->shouldReceive('limit')
            ->andReturn($this->mockDatabase);
        $dbResult = [
            'results' => [
                $conflict,
            ]
        ];
        $mockResponse = $this->mockBatchResponse($dbResult);
        $mockResponse
            ->shouldReceive('hits')
            ->once()
            ->andReturn('1'); //Clusterpoint returns number as string
        $mockResponse
            ->shouldReceive('offsetGet')
            ->once()
            ->andReturn($conflict);
        $this->mockDatabase
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockResponse);
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->createFromObject($resource);
        $this->expectException(RestException::class);
        $model->validateUniqueFields();
    }

    public function testPostWithDatabaseFailure()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('post')
            ->andThrow(RestException::class, 'Database operation failed');
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->createFromObject($resource);
        try {
            $model->create();
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Database operation failed', $ex->getMessage());
        }
    }

    public function testPutWithDatabaseFailure()
    {
        $resource = new \StdClass;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('put')
            ->andThrow(RestException::class, 'Database operation failed');
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->createFromObject($resource);
        try {
            $model->update();
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Database operation failed', $ex->getMessage());
        }
    }
}
