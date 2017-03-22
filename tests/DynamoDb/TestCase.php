<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Aws\Result;
use Aws\DynamoDb\Marshaler;

abstract class TestCase extends PHPUnitTestCase
{
    public function mockResponse($object)
    {
        $marshaler = new Marshaler;
        $result = new Result(['Item' => $marshaler->marshalItem($object)]);
        return $result;
    }

    public function mockBatchResponse($list)
    {
        $marshaler = new Marshaler;
        $items = [];
        foreach ($list as $item) {
            $items[] = $marshaler->marshalItem($item);
        }
        $result = new Result(['Items' => $items]);
        return $result;
    }
}
