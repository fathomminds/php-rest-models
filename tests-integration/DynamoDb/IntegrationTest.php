<?php
namespace Fathomminds\Rest\Tests\Integration\DynamoDb;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Helpers\Uuid;
use Fathomminds\Rest\Examples\DynamoDb\Models\FooModel;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FooSchema;

class IntegrationTest extends TestCase
{
    public function testOnRealData()
    {
        $model = new FooModel;
        $resource = new FooSchema;
        $resource->_id = (new Uuid)->generate();
        $resource->title = 'CREATED';
        $model->resource($resource);

        $model->create();
        $id = $model->resource()->_id;
        $this->assertTrue(!empty($id));

        try {
            $model->create();
            $this->assertEquals(1, 0); //Shouldn't reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Primary key collision', $ex->getMessage());
        }

        $model = new FooModel;
        $list = $model->all();
        $this->assertCount(1, $list);
        $this->assertEquals($id, $list[0]->_id);
        $this->assertEquals('CREATED', $list[0]->title);

        $model = new FooModel;
        $model->one($id);
        $this->assertEquals($id, $model->resource()->_id);
        $this->assertEquals('CREATED', $model->resource()->title);

        $model->resource()->title = 'REPLACED';
        $model->replace();

        $model = new FooModel;
        $model->one($id);
        $this->assertEquals($id, $model->resource()->_id);
        $this->assertEquals('REPLACED', $model->resource()->title);

        $model->delete();
        $this->assertTrue(empty(get_object_vars($model->resource())));

        try {
            $model = new FooModel;
            $model->one($id);
        } catch (RestException $ex) {
            $this->assertEquals('Resource does not exist', $ex->getMessage());
        }
    }
}
