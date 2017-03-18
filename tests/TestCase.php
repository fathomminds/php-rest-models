<?php
namespace Fathomminds\Rest\Tests;

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
        $this->mockClient->shouldReceive('database')->once()->andReturn($this->mockDatabase);
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
        $ReflectionHelper = new ReflectionHelper;
        $object = $this->mockObject($objectClassName);
        $model = $ReflectionHelper->createInstance($modelClassName, [$object]);
        return $model;
    }

    public function mockObject($objectClassName)
    {
        $ReflectionHelper = new ReflectionHelper;
        $object = $ReflectionHelper->createInstance(
            $objectClassName,
            [
              null,
              null,
              new Database($this->mockClient)
            ]
        );
        return $object;
    }
}
