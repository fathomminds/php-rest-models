<?php
namespace Fathomminds\Rest\Tests\Integration\Clusterpoint;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FooModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\BarSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FlopSchema;
use Clusterpoint\Client;

class IntegrationTest extends TestCase
{
    public function testOnRealData()
    {
        $model = new FooModel;
        $resource = new FooSchema;
        $barSchema = new BarSchema;
        $barSchema->flip = 'flip';
        $resource->title = 'CREATED';
        $resource->status = 0;
        $resource->bar = [
            $barSchema,
        ];
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
        $flopSchema = new FlopSchema;
        $flopSchema->_id = "TESTID";
        $flopSchema->mobile = "TESTVALUE";
        $barSchema->flip = "flipUpdated";
        $barSchema->flop = $flopSchema;
        $updateResource->_id = $id;
        $updateResource->status = 1;
        $updateResource->bar = [
            $barSchema,
        ];
        $model->resource($updateResource);

        try {
            $model->update();
            $this->fail();
        } catch (RestException $ex) {
            $this->assertEquals('Error: Property does not exist.', $ex->getMessage());
        } catch (\Exception $ex) {
            $this->fail();
        }

        unset($updateResource->bar[0]->flop);
        $model->resource($updateResource);
        $model->update();

        $model = new FooModel;
        $model->one($id);
        $this->assertEquals($id, $model->resource()->_id);
        $this->assertEquals(1, $model->resource()->status);
        $this->assertEquals("flipUpdated", $model->resource()->bar[0]->flip);
        $this->assertFalse(property_exists($model->resource()->bar[0], 'flop'));

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
