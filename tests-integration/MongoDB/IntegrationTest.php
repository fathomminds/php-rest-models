<?php
namespace Fathomminds\Rest\Tests\Integration\MongoDB;

use Fathomminds\Rest\Examples\MongoDB\Models\FooModel;
use Fathomminds\Rest\Examples\MongoDB\Models\Schema\FooSchema;
use Fathomminds\Rest\Exceptions\RestException;
use Mockery;

class IntegrationTest extends TestCase
{
    public function testCreate()
    {
        try {
            $resource = new FooSchema;
            $resource->title = "TITLE";
            $foo = new FooModel;
            $foo->resource($resource);
            $foo->create();
            $id1 = $foo->resource()->_id;
            $this->assertEquals("TITLE", $foo->resource()->title);

            $resource = new FooSchema;
            $resource->title = "TITLE 2";
            $foo = new FooModel;
            $foo->resource($resource);
            $foo->create();
            $id2 = $foo->resource()->_id;
            $this->assertEquals("TITLE 2", $foo->resource()->title);

            $foo = new FooModel;
            $foo->one($id1);
            $this->assertEquals("TITLE", $foo->resource()->title);

            $foo = new FooModel;
            $foo->one($id2);
            $this->assertEquals("TITLE 2", $foo->resource()->title);

            $foo = new FooModel;
            $foo->resource((object)[
                '_id' => $id1,
                'title' => "UPDATE 1",
            ]);
            $foo->update();
            $foo->one($id1);
            $this->assertEquals("UPDATE 1", $foo->resource()->title);
            $this->assertEquals(0, $foo->resource()->status);

            $foo = new FooModel;
            $resource = new FooSchema;
            $resource->_id = $id2;
            $resource->title = "REPLACE";
            $resource->status = 1;
            $foo->resource($resource);
            $foo->replace();
            $foo->one($id2);
            $this->assertEquals("REPLACE", $foo->resource()->title);

            $foo = new FooModel;
            $res = $foo->all();
            $this->assertEquals(2, count($res));
            $this->assertEquals("UPDATE 1", $res[0]->title);
            $this->assertEquals("REPLACE", $res[1]->title);

            $foo = new FooModel;
            $resource = new FooSchema;
            $resource->_id = $id1;
            $foo->resource($resource);
            $foo->delete();

            try {
                $foo = new FooModel;
                $foo->one($id1);
                $this->fail();
            } catch (RestException $ex) {
                $this->assertEquals("Resource does not exist", $ex->getMessage());
            }

            $foo = new FooModel;
            $resource = new FooSchema;
            $resource->_id = $id2;
            $foo->resource($resource);
            $foo->delete();

            try {
                $foo = new FooModel;
                $foo->one($id2);
                $this->fail();
            } catch (RestException $ex) {
                $this->assertEquals("Resource does not exist", $ex->getMessage());
            }

            $foo = new FooModel;
            $res = $foo->all();
            $this->assertEquals(0, count($res));

        } catch (RestException $ex) {
            var_dump($ex->getMessage());
            var_dump($ex->getDetails());
        }
    }
}
