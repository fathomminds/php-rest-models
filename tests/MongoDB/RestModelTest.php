<?php
namespace Fathomminds\Rest\Tests\MongoDB;

use Fathomminds\Rest\Examples\MongoDB\Models\FooModel;
use Fathomminds\Rest\Examples\MongoDB\Models\Objects\FooObject;
use Mockery;

class RestModelTest extends TestCase
{
    public function testConstruct()
    {
        $foo = new FooModel;

        $class = new \ReflectionClass($foo);
        $property = $class->getProperty('restObject');
        $property->setAccessible(true);
        $restObject = $property->getValue($foo);
        $className = get_class($restObject);
        $this->assertEquals(FooObject::class, $className);
    }
}
