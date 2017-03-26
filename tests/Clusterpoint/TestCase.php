<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Mockery;
use Clusterpoint\Client;
use Clusterpoint\Instance\Service;
use Clusterpoint\Response\Single;
use Clusterpoint\Response\Batch;
use Fathomminds\Rest\Database\Clusterpoint\Database;
use Fathomminds\Rest\Helpers\ReflectionHelper;

abstract class TestCase extends PHPUnitTestCase
{
    protected $mockDatabase;
    protected $mockClient;

    public function __construct()
    {
        $this->mockDatabase = Mockery::mock(Service::class);
        $this->mockClient = Mockery::mock(Client::class);
        $this->mockClient->shouldReceive('database')->andReturn($this->mockDatabase);
    }

    public function mockResponse($rawResponse, $errors = [])
    {
        $mockResponse = Mockery::mock(Single::class);
        $mockResponse->shouldReceive('error')->once()->andReturn($errors);
        $mockResponse->shouldReceive('rawResponse')->once()->andReturn(json_encode($rawResponse));
        return $mockResponse;
    }

    public function mockBatchResponse($rawResponse, $errors = [])
    {
        $mockResponse = Mockery::mock(Batch::class);
        $mockResponse->shouldReceive('error')->once()->andReturn($errors);
        $mockResponse->shouldReceive('rawResponse')->once()->andReturn(json_encode($rawResponse));
        return $mockResponse;
    }

    public function mockModel($modelClassName = null, $objectClassName = null)
    {
        $reflectionHelper = new ReflectionHelper;
        $object = gettype($objectClassName) === 'object' ? $objectClassName : $this->mockObject($objectClassName);
        $model = $reflectionHelper->createInstance($modelClassName, [$object]);
        return $model;
    }

    public function mockObject($objectClassName)
    {
        $reflectionHelper = new ReflectionHelper;
        $object = $reflectionHelper->createInstance(
            $objectClassName,
            [
              null,
              null,
              new Database($this->mockClient)
            ]
        );
        return $object;
    }

    public function mockObjectValidationOk($resource)
    {
        $mockObject = Mockery::mock(FooObject::class);
        $mockObject
            ->shouldReceive('validateUniqueFields')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('setProperty')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('createFromObject')
            ->andReturn($mockObject);
        $resource = new \StdClass;
        $resource->title = 'REQUIRED';
        $mockObject
            ->shouldReceive('resource')
            ->andReturn($resource);
        $mockObject
            ->shouldReceive('validateSchema')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('validate')
            ->andReturn(null);
        $mockObject
            ->shouldReceive('getPrimaryKeyValue')
            ->andReturn(property_exists($resource, '_id') ? $resource->_id : null);
        return $mockObject;
    }
}
