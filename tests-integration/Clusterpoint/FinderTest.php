<?php
namespace Fathomminds\Rest\Tests\Integration\Clusterpoint;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Helpers\Uuid;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FinderModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FinderSchema;

class FinderTest extends TestCase
{
    private function seed()
    {
        $model = new FinderModel;
        $all = $model->all();
        $ids = array_column($all, '_id');
        if (!empty($ids)) {
            $model->query()->deleteMany($ids);
        }
        for ($i=0; $i<100; $i++) {
            $resource = new FinderSchema;
            $resource->_id = (new Uuid)->generate();
            $resource->title = 'ITEM-' . $i;
            $resource->status = $i;
            $model->resource($resource);
            $model->create();
        }
    }

    // public function testSeed()
    // {
    //     $this->seed();
    // }

    public function testFindByTitle()
    {
        $model = new FinderModel;
        $res = $model->find()
            ->where([
              ['title', '==', 'ITEM-1'],
            ])
            ->get()
            ->first();
        $this->assertEquals('ITEM-1', $res->title);
    }

    public function testFindByStatus()
    {
        $model = new FinderModel;
        $res = $model->find()
            ->where([
              ['status', '==', 1],
            ])
            ->get()
            ->first();
        $this->assertEquals('ITEM-1', $res->title);
    }

    public function testFindManyByStatus()
    {
        $model = new FinderModel;
        $res = $model->find()
            ->where([
              ['status', '<', 10],
            ])
            ->get()
            ->all();
        $this->assertEquals(10, count($res));
    }

    public function testFindManyByStatusWithLimit()
    {
        $model = new FinderModel;
        $res = $model->find()
            ->where([
              ['status', '<', 10],
            ])
            ->limit(1)
            ->get()
            ->all();
        $this->assertEquals(1, count($res));
    }

    public function testFindManyByStatusWithLimitAndOffset()
    {
        $model = new FinderModel;
        $res = $model->find()
            ->where([
              ['status', '<', 10],
            ])
            ->orderBy(['title', 'ASC'])
            ->limit(1)
            ->offset(1)
            ->get()
            ->all();
        $this->assertEquals(1, count($res));
        $this->assertEquals('ITEM-1', $res[0]->title);
    }

    public function testFindManyByStatusWithLimitAndOffsetOrderDesc()
    {
        $model = new FinderModel;
        $res = $model->find()
            ->where([
              ['status', '<', 10],
            ])
            ->orderBy(['title', 'DESC'])
            ->limit(1)
            ->offset(1)
            ->get()
            ->all();
        $this->assertEquals(1, count($res));
        $this->assertEquals('ITEM-8', $res[0]->title);
    }
}
