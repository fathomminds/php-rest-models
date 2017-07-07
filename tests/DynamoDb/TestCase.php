<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Result;
use Aws\DynamoDb\Marshaler;
use Mockery;
use Fathomminds\Rest\Helpers\ReflectionHelper;
use Fathomminds\Rest\Database\DynamoDb\Database;

abstract class TestCase extends PHPUnitTestCase
{
    public $mockClient;

    public function __construct()
    {
        $this->mockClient = Mockery::mock(DynamoDbClient::class);
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

    public function mockModel($modelClassName = null, $objectClassName = null)
    {
        $reflectionHelper = new ReflectionHelper;
        $object = $this->mockObject($objectClassName);
        $model = $reflectionHelper->createInstance($modelClassName, [$object]);
        return $model;
    }

    public function mockResponse($array)
    {
        $return = [];
        foreach ($array as $key => $value) {
            $return[$key] = $this->marshalItem($value);
        }
        $result = new Result($return);
        return $result;
    }

    public function mockBatchResponse($array)
    {
        $return = [];
        $items = [];
        if (isset($array['Items'])) {
            foreach ($array['Items'] as $item) {
                $marshaledItem = $this->marshalItem($item);
                $items[] = $marshaledItem;
            }
            unset($array['Items']);
        }
        foreach ($array as $key => $value) {
            switch ($key) {
                case 'LastEvaluatedKey':
                    $return[$key] = $this->marshalItem($value);
                    break;
                default:
                    $return[$key] = $value;
            }
        }
        $return['Items'] = $items;
        $result = new Result($return);
        return $result;
    }

    public function marshalItem($resource)
    {
        $marshaler = new Marshaler;
        $toMarshal = $resource;
        if (gettype($resource) === 'object') {
            $toMarshal = new \StdClass;
            foreach (get_object_vars($resource) as $name => $value) {
                $toMarshal->$name = $value;
            }
        }
        return $marshaler->marshalItem($toMarshal);
    }

    public function resource($array)
    {
        return json_decode(json_encode($array));
    }

    public function tearDown()
    {
        parent::tearDown();
        Mockery::close();
    }
}
