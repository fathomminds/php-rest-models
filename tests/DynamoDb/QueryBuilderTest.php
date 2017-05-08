<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use Mockery;
use Aws\DynamoDb\DynamoDbClient as Client;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\DynamoDb\Finder;
use Fathomminds\Rest\Examples\DynamoDb\Models\FinderModel;

class QueryBuilderTest extends TestCase
{
    public function testModelFind()
    {
        $mockClient = Mockery::mock(Client::class);
        $model = new FinderModel;
        $res = $model->find($mockClient)->first();
        $this->assertNull($res);
    }

    public function testModelFindCreateClient()
    {
        $model = new FinderModel;
        $res = $model->find();
        $this->assertEquals(Finder::class, get_class($res));
    }

    public function testFinderCreateClient()
    {
        $finder = new Finder;
        $property = new \ReflectionProperty($finder, 'client');
        $property->setAccessible(true);
        $this->assertEquals(Client::class, get_class($property->getValue($finder)));
    }

    public function testFinderGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $res = $finder->get()->first();
        $this->assertNull($res);
    }
}
