<?php
namespace Fathomminds\Rest\Tests\Integration\Clusterpoint;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FooModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\BarSchema;
use Clusterpoint\Client;

class IntegrationTest extends TestCase
{
    public function testOnRealData()
    {
        $model = new FooModel;
        $resource = new FooSchema;
        $resource->title = 'CREATED';
        $resource->status = 0;
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

        $model = new FooModel;
        $updateResource = new FooSchema;
        $barSchema = new BarSchema;
        $barSchema->flip = "flip";
        $updateResource->_id = $id;
        $updateResource->status = 1;
        $updateResource->bar = [
            $barSchema,
        ];
        $model->resource($updateResource);
        $model->update();

        $model = new FooModel;
        $model->one($id);
        $this->assertEquals($id, $model->resource()->_id);
        $this->assertEquals(1, $model->resource()->status);
        $this->assertEquals("flip", $model->resource()->bar[0]->flip);

        $model->delete();
        $this->assertTrue(empty(get_object_vars($model->resource())));

        try {
            $model = new FooModel;
            $model->one($id);
        } catch (RestException $ex) {
            $this->assertEquals('Resource does not exist', $ex->getMessage());
        }
    }

    /**
     * Testing Clusterpoint code (collection replace() works correctly on nested objects)
     */
    public function testClusterpointReplaceWorks()
    {
        $cp = new Client;

        $barSchema = new BarSchema;
        $barSchema->flip = "FLIP";
        $fooSchema = new FooSchema;
        $fooSchema->title = "TITLE";
        $fooSchema->bar = [
            $barSchema,
        ];

        $fooModel = new FooModel;
        $collection = $cp->database(getenv('CLUSTERPOINT_DATABASE').".foo");

        $res = null;
        $insertId = null;
        try {
            $res = $collection->insertOne($fooSchema);
            $insertId = $res->_id;
        } catch (\Exception $ex) {
            $this->assertEquals(1, 0); // Must not reach this line
        }

        $fooSchema->title = "TITLE_REPLACED";
        $fooSchema->bar[0]->flip = "FLIP_REPLACED";

        try {
            $collection->replace($insertId, $fooSchema);
        } catch (\Exception $ex) {
            $this->assertEquals(1, 0); // Must not reach this line
        }

        try {
            $res = $collection->where("_id", $insertId)->get();
            $fooData = json_decode($res->rawResponse(), true)["results"][0];
            $this->assertEquals($fooData["title"], "TITLE_REPLACED");
            $this->assertEquals($fooData["bar"][0]["flip"], "FLIP_REPLACED");
        } catch (\Exception $ex) {
            $this->assertEquals(1, 0); // Must not reach this line
        }

        try {
            $collection->delete($insertId);
        } catch (\Exception $ex) {
            $this->assertEquals(1, 0); // Must not reach this line
        }
    }
}
