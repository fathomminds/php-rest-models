<?php
namespace Fathomminds\Rest\Tests\Integration\MongoDB;

use Fathomminds\Rest\Examples\MongoDB\Models\FooModel;
use Fathomminds\Rest\Examples\MongoDB\Models\Schema\FooSchema;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Helpers\Uuid;
use Mockery;

class FinderTest extends TestCase
{
    private function cleanAndSeed() {
        $this->clean();
        $this->seed();
    }

    private function clean() {
        $foo = new FooModel;
        $all = $foo->all();
        foreach($all as $item) {
            $foo->resource($item);
            $foo->delete();
        }
    }

    private function seed() {
        $foo = new FooModel;
        for ($i = 0; $i < 100; $i++) {
            $resource = new FooSchema;
            $resource->title = (new Uuid())->generate();
            $resource->status = rand(0, 10);
            $foo->resource($resource);
            $foo->create();
        }
    }

    public function testCreate()
    {
        try {
            $this->cleanAndSeed();
            $foo = new FooModel;
            $res = $foo->find()
                ->database('restmodels')
                ->select(['_id', 'title', 'status'])
                ->from('foo')
                ->where([
                    'OR' => [
                        ['status', '==', 7],
                        'AND' => [
                            ['status', '>', 3],
                            ['status', '<=', 5],
                        ],
                    ],
                ])
                ->get()
                ->all();

            foreach ($res as $item) {
                $check =
                    $item->status === 4 ||
                    $item->status === 5 ||
                    $item->status === 7;
                $this->assertEquals($check, true);
            }

            $res = $foo->find()
                ->database('restmodels')
                ->select('*')
                ->from('foo')
                ->orderBy('status', 'ASC')
                ->get()
                ->all();

            $previousStatus = -1;
            $itemId = null;
            foreach ($res as $item) {
                $check = $item->status >= $previousStatus;
                $this->assertEquals($check, true);
                $previousStatus = $item->status;
                $itemId = $item->_id;
            }

            $item = $foo->find()
                ->database('restmodels')
                ->select(['_id'])
                ->from('foo')
                ->where([
                    ['_id', '==', $itemId]
                ])
                ->get()
                ->first();
            $this->assertEquals($itemId, $item->_id);

        } catch (RestException $ex) {
            var_dump($ex->getMessage());
            var_dump($ex->getDetails());
        } finally {
            $this->clean();
        }
    }
}
