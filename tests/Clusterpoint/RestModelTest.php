<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\BarSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FlopSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FooObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FooModel;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Schema\TypeValidators\ArrayValidator;
use Mockery;

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
        $obj = new FooSchema;
        $obj->_id = 'ID';
        $obj->title = 'TITLE';
        $foo->resource($obj);
        $this->assertEquals('ID', $foo->resource()->_id);
        $this->assertEquals('TITLE', $foo->resource()->title);
    }

    public function testGetResource()
    {
        $foo = $this->mockModel(FooModel::class, FooObject::class);
        $obj = new FooSchema;
        $obj->_id = 'ID';
        $obj->title = 'TITLE';
        $foo->resource($obj);
        $this->assertEquals($obj, $foo->resource());
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
        $resource = new FooSchema;
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
        $this->assertEquals('ID', $model->resource()->_id);
    }

    public function testAll()
    {
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('limit')
            ->andReturn($this->mockDatabase);
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
        $resource = new FooSchema;
        $resource->_id = 'NEW';
        // $resource->title = 'TITLE'; // Skipping required field for update
        $resource->status = 1;
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('setFieldDefaults')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('patch')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('updateMode')
            ->andReturn(true);
        $mockObject
            ->shouldReceive('replaceMode')
            ->andReturn(false);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->resource($resource);
        try {
            $model->update();
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testReplaceValidStructure()
    {
        $resource = new FooSchema;
        $resource->_id = 'NEW';
        $resource->title = 'TITLE';
        $resource->status = 0;
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('setFieldDefaults')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('put')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('updateMode')
            ->andReturn(false);
        $mockObject
            ->shouldReceive('replaceMode')
            ->andReturn(true);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->resource($resource);
        try {
            $model->replace();
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testCreateValidStructure()
    {
        $resource = new FooSchema;
        $resource->_id = 'NEW';
        $resource->title = 'TITLE';
        $resource->status = 0;
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('updateMode')
            ->andReturn(false);
        $mockObject
            ->shouldReceive('replaceMode')
            ->andReturn(false);
        $mockObject
            ->shouldReceive('setFieldDefaults')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('post')
            ->andReturn(null);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->resource($resource);
        try {
            $model->create();
            $this->assertTrue(true);
        } catch (\Exception $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testDeleteNonExisting()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('delete')
            ->once()
            ->andReturn($this->mockResponse($dbResult, ['Assume DB failure']));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->resource($resource);
        $this->expectException(RestException::class);
        $deletedId = $model->delete();
    }

    public function testDeleteExisting()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $dbResult = [
            'results' => []
        ];
        $this->mockDatabase
            ->shouldReceive('delete')
            ->once()
            ->andReturn($this->mockResponse($dbResult));
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->resource($resource);
        $deletedId = $model->delete();
        $this->assertEquals('ID', $deletedId);
    }

    public function testValidateInvalid()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->resource($resource);
        try {
            $model->validate();
            $this->fail(); //Should throw exception
        } catch (RestException $ex) {
            $this->assertEquals('Invalid structure', $ex->getMessage());
        }
    }

    public function testValidateValid()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $mockObject = $this->mockObjectValidationOk($resource);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->resource($resource);
        try {
            $model->validate();
            $this->assertTrue(true); //Should reach this line
        } catch (RestException $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testSetProperty()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->resource($resource);
        $model->resource()->title = 'OTHER';
        $this->assertEquals('OTHER', $model->resource()->title);
    }

    public function testGettingNonExistentProperty()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->resource($resource);
        $this->assertTrue(!isset($model->resource()->noSuchProperty));
    }

    public function testToArray()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->resource($resource);
        $array = $model->toArray();
        $this->assertArrayHasKey('_id', $array);
        $this->assertEquals('ID', $array['_id']);
        $this->assertArrayHasKey('title', $array);
        $this->assertEquals('TITLE', $array['title']);
    }

    public function testValidateUniqueFieldsNoConflict()
    {
        $resource = new FooSchema;
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
        $model->resource($resource);
        $model->validate();
        $this->assertEquals(1, 1); //Reaching this line if no exception is thrown
    }

    public function testValidateUniqueFieldsConflict()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $conflict = new FooSchema;
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
        $this->mockDatabase
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockResponse);
        $model = $this->mockModel(FooModel::class, FooObject::class);
        $model->resource($resource);
        $this->expectException(RestException::class);
        $model->validate();
    }

    public function testPostWithDatabaseFailure()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('setFieldDefaults')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('post')
            ->andThrow(RestException::class, 'Database operation failed');
        $mockObject
            ->shouldReceive('updateMode')
            ->andReturn(false);
        $mockObject
            ->shouldReceive('replaceMode')
            ->andReturn(false);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->resource($resource);
        try {
            $model->create();
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Database operation failed', $ex->getMessage());
        }
    }

    public function testPutWithDatabaseFailure()
    {
        $resource = new FooSchema;
        $resource->_id = 'ID';
        $resource->title = 'TITLE';
        $mockObject = $this->mockObjectValidationOk($resource);
        $mockObject
            ->shouldReceive('setFieldDefaults')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('put')
            ->andThrow(RestException::class, 'Database operation failed');
        $mockObject
            ->shouldReceive('updateMode')
            ->andReturn(false);
        $mockObject
            ->shouldReceive('replaceMode')
            ->andReturn(true);
        $model = $this->mockModel(FooModel::class, $mockObject);
        $model->resource($resource);
        try {
            $model->replace();
            $this->fail(); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Database operation failed', $ex->getMessage());
        }
    }

    public function testQuery()
    {
        $model = new FooModel;
        $q = $model->query();
        $this->assertEquals('Clusterpoint\Instance\Service', get_class($q));
    }

    public function testSetDatabaseName()
    {
        $testDabataseName = 'TestDatabaseName';
        $mockRestObject = Mockery::mock(FooObject::class);
        $mockRestObject->shouldReceive('setDatabaseName')->once();
        $model = new FooModel($mockRestObject);
        $model->setDatabaseName($testDabataseName);
        $this->assertEquals(1, 1); // Must reach this line
    }

    public function testGetDatabaseName()
    {
        $mockRestObject = Mockery::mock(FooObject::class);
        $mockRestObject->shouldReceive('getDatabaseName')->withNoArgs()->once();
        $model = new FooModel($mockRestObject);
        $model->getDatabaseName();
        $this->assertEquals(1, 1); // Must reach this line
    }

    public function testSchemaCast()
    {
        $object = (object)[
            '_id' => 'FOOID',
            'status' => 1,
            'bar' => [
                (object)[
                    '_id' => 'BARID',
                    'flip' => 'FLIP',
                    'flop' => (object)[
                        '_id' => 'FLOPID',
                        'mobile' => 'MOBILE',
                        'addrLat' => 13,
                        'addrLng' => 31.75,
                        'extraneousField' => 'EXTRA',
                    ],
                ],
            ],
        ];
        $fooSchema = new FooSchema($object);
        $this->assertEquals(
            'Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema',
            get_class($fooSchema)
        );
        $this->assertTrue(is_string($fooSchema->_id));
        $this->assertEquals($fooSchema->_id, 'FOOID');
        $this->assertTrue(is_integer($fooSchema->status));
        $this->assertEquals($fooSchema->status, 1);
        $this->assertTrue(is_array($fooSchema->bar));
        $this->assertEquals(
            'Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\BarSchema',
            get_class($fooSchema->bar[0])
        );
        $this->assertTrue(is_string($fooSchema->bar[0]->_id));
        $this->assertEquals($fooSchema->bar[0]->_id, 'BARID');
        $this->assertTrue(is_string($fooSchema->bar[0]->flip));
        $this->assertEquals($fooSchema->bar[0]->flip, 'FLIP');
        $this->assertEquals(
            'Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FlopSchema',
            get_class($fooSchema->bar[0]->flop)
        );
        $this->assertTrue(is_string($fooSchema->bar[0]->flop->_id));
        $this->assertEquals($fooSchema->bar[0]->flop->_id, 'FLOPID');
        $this->assertTrue(is_string($fooSchema->bar[0]->flop->mobile));
        $this->assertEquals($fooSchema->bar[0]->flop->mobile, 'MOBILE');
        $this->assertTrue(is_double($fooSchema->bar[0]->flop->addrLat));
        $this->assertEquals($fooSchema->bar[0]->flop->addrLat, 13);
        $this->assertTrue(is_double($fooSchema->bar[0]->flop->addrLng));
        $this->assertEquals($fooSchema->bar[0]->flop->addrLng, 31.75);
        $this->assertTrue(property_exists($fooSchema->bar[0]->flop, 'extraneousField'));
    }

    public function testInvalidCastParams()
    {
        $value = null;
        $params = null;
        $this->assertEquals(null, ArrayValidator::cast($value, $params));

        $value = ['paramsIsNotArray'];
        $params = null;
        $this->assertEquals('["paramsIsNotArray"]', json_encode(ArrayValidator::cast($value, $params)));

        $value = ['missingParamsKey'];
        $params = ['item' => ['validator' => 'INTEGER']];
        $this->assertEquals('["missingParamsKey"]', json_encode(ArrayValidator::cast($value, $params)));

        $value = ['missingParamsItem'];
        $params = ['key' => ['validator' => 'INTEGER']];
        $this->assertEquals('["missingParamsItem"]', json_encode(ArrayValidator::cast($value, $params)));
    }
}
