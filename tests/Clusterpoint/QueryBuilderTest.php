<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Clusterpoint\Client;
use Clusterpoint\Response\Response;
use Clusterpoint\Response\Batch;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Database\Clusterpoint\Finder;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FinderModel;

class QueryBuilderTest extends TestCase
{
    public function testMethodConstruct()
    {
        $finder = new Finder;
        $property = new \ReflectionProperty($finder, 'client');
        $property->setAccessible(true);
        $client = $property->getValue($finder);
        $this->assertEquals(Client::class, get_class($client));

        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $property = new \ReflectionProperty($finder, 'client');
        $property->setAccessible(true);
        $client = $property->getValue($finder);
        $this->assertEquals(get_class($mockClient), get_class($client));
    }

    public function testMethodDatabase()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $finder->database('DATABASE');
        $property = new \ReflectionProperty($finder, 'databaseName');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals('DATABASE', $value);
    }

    public function testMethodSelect()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $arg = ['F1', 'F2'];
        $finder->select($arg);
        $property = new \ReflectionProperty($finder, 'select');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertTrue(in_array('F1', $value));
        $this->assertTrue(in_array('F2', $value));
        $this->assertTrue(2 === count($value));
    }

    public function testMethodFrom()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $finder->from('COLLECTION');
        $property = new \ReflectionProperty($finder, 'from');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals('COLLECTION', $value);
    }

    public function testMethodOrderBy()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $finder->orderBy('F', 'ASC');
        $property = new \ReflectionProperty($finder, 'orderBy');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals([0 => ['F'=>'ASC']], $value);
    }

    public function testMethodLimit()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $finder->limit(9);
        $property = new \ReflectionProperty($finder, 'limit');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals(9, $value);
    }

    public function testMethodOffset()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $finder->offset(9);
        $property = new \ReflectionProperty($finder, 'offset');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals(9, $value);
    }

    public function testMethodFirst()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $property = new \ReflectionProperty($finder, 'resultSet');
        $property->setAccessible(true);
        $value = $property->setValue($finder, [1, 2, 3, 4, 5]);
        $this->assertEquals(1, $finder->first());
    }

    public function testMethodAll()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $property = new \ReflectionProperty($finder, 'resultSet');
        $property->setAccessible(true);
        $value = $property->setValue($finder, [1, 2, 3, 4, 5]);
        $this->assertEquals([1, 2, 3, 4, 5], $finder->all());
    }

    public function testMethodWhereWithValidConditions()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $where = [
            'OR' => [
                ['F1', '==', 'V1'],
                ['F2', '!=', 'V2'],
            ],
        ];
        $finder->where($where);
        $property = new \ReflectionProperty($finder, 'where');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals($where, $value);
    }

    public function testMethodWhereWithInValidConditionsInvalidGroup()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $where = [
            'INVALID' => [
                ['F1', '==', 'V1'],
                ['F2', '!=', 'V2'],
            ],
        ];
        try {
            $finder->where($where);
            $this->fail();
        } catch (RestException $ex) {
            $this->assertEquals('Invalid where condition', $ex->getMessage());
        }
    }

    public function testMethodWhereWithInValidConditionsInvalidOperator()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $where = [
            'OR' => [
                ['F1', 'INVALID', 'V1'],
                ['F2', '!=', 'V2'],
            ],
        ];
        try {
            $finder->where($where);
            $this->fail();
        } catch (RestException $ex) {
            $this->assertEquals('Invalid operator in where condition', $ex->getMessage());
        }
    }

    public function testMethodWhereWithEmptyArray()
    {
        $mockClient = Mockery::mock(Client::class);
        $finder = new Finder($mockClient);
        $where = [];
        $finder->where($where);
        $property = new \ReflectionProperty($finder, 'where');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals($where, $value);
    }

    public function testClusterpointMethodGet()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockResponse = Mockery::mock(Response::class);
        $mockResult = Mockery::mock(Batch::class);
        $item = new \StdClass;
        $item->F1 = 'V1';
        $item->F2 = 'V22';
        $mockResultSet = [
            $item,
        ];
        $mockClient
            ->shouldReceive('database')
            ->once()
            ->andReturn($mockResponse);
        $mockResponse
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockResult);
        $mockResponse
            ->shouldReceive('where')
            ->once();
        $mockResult
            ->shouldReceive('toArray')
            ->andReturn($mockResultSet);
        $finder = new Finder($mockClient);
        $where = [
            'OR' => [
                ['F1', '==', 'V1'],
                ['F2', '!=', 'V2'],
            ],
        ];
        $finder
            ->where($where)
            ->get();
        $property = new \ReflectionProperty($finder, 'resultSet');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals($mockResultSet, $value);
    }

    public function testClusterpointMethodGetWithFinderParams()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockResponse = Mockery::mock(Response::class);
        $mockResult = Mockery::mock(Batch::class);
        $item = new \StdClass;
        $item->F1 = 'V1';
        $item->F2 = 'V22';
        $mockResultSet = [
            $item,
        ];
        $mockClient
            ->shouldReceive('database')
            ->once()
            ->andReturn($mockResponse);
        $mockResponse
            ->shouldReceive('select')
            ->once();
        $mockResponse
            ->shouldReceive('limit')
            ->once();
        $mockResponse
            ->shouldReceive('offset')
            ->once();
        $mockResponse
            ->shouldReceive('orderBy')
            ->once();
        $mockResponse
            ->shouldReceive('where')
            ->never();
        $mockResponse
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockResult);
        $mockResult
            ->shouldReceive('toArray')
            ->andReturn($mockResultSet);
        $finder = new Finder($mockClient);
        $where = [];
        $finder
          ->select(['F1'])
          ->limit(1)
          ->offset(0)
          ->orderBy('F1', 'ASC')
          ->where($where)
          ->get();
        $property = new \ReflectionProperty($finder, 'resultSet');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals($mockResultSet, $value);
    }

    public function testClusterpointMethodGetWithMainLogicalAnd()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockResponse = Mockery::mock(Response::class);
        $mockResult = Mockery::mock(Batch::class);
        $item = new \StdClass;
        $item->F1 = 'V1';
        $item->F2 = 'V22';
        $mockResultSet = [
            $item,
        ];
        $mockClient
            ->shouldReceive('database')
            ->once()
            ->andReturn($mockResponse);
        $mockResponse
            ->shouldReceive('where');
        $mockResponse
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockResult);
        $mockResult
            ->shouldReceive('toArray')
            ->andReturn($mockResultSet);
        $finder = new Finder($mockClient);
        $where = [
            'AND' => [
                ['F1', '==', 'V1'],
                ['F2', '==', 'V2'],
            ],
            'OR' => [
                ['F3', '==', 'V3'],
                ['F4', '==', 'V4'],
            ],
            ['F5', '==', 'V5'],
        ];
        $finder
          ->where($where)
          ->get();
        $property = new \ReflectionProperty($finder, 'resultSet');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals($mockResultSet, $value);
    }

    public function testClusterpointMethodGetWithMainLogicalOr()
    {
        $mockClient = Mockery::mock(Client::class);
        $mockResponse = Mockery::mock(Response::class);
        $mockResult = Mockery::mock(Batch::class);
        $item = new \StdClass;
        $item->F1 = 'V1';
        $item->F2 = 'V22';
        $mockResultSet = [
            $item,
        ];
        $mockClient
            ->shouldReceive('database')
            ->once()
            ->andReturn($mockResponse);
        $mockResponse
            ->shouldReceive('orWhere');
        $mockResponse
            ->shouldReceive('where');
        $mockResponse
            ->shouldReceive('get')
            ->once()
            ->andReturn($mockResult);
        $mockResult
            ->shouldReceive('toArray')
            ->andReturn($mockResultSet);
        $finder = new Finder($mockClient);
        $where = [
            'AND' => [
                ['F1', '==', 'V1'],
                ['F2', '==', 'V2'],
            ],
            'OR' => [
                ['F3', '==', 'V3'],
                ['F4', '==', 'V4'],
            ],
            ['F5', '==', 'V5'],
        ];
        $property = new \ReflectionProperty($finder, 'mainLogical');
        $property->setAccessible(true);
        $property->setValue($finder, '||');
        $finder
          ->where($where)
          ->get();
        $property = new \ReflectionProperty($finder, 'resultSet');
        $property->setAccessible(true);
        $value = $property->getValue($finder);
        $this->assertEquals($mockResultSet, $value);
    }

    public function testModelFind()
    {
        $mockClient = Mockery::mock(Client::class);
        $model = new FinderModel;
        $res = $model->find($mockClient)->first();
        $this->assertNull($res);
    }
}
