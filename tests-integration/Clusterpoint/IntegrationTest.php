<?php
namespace Fathomminds\Rest\Tests\Integration\Clusterpoint;

use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FooModel;

class IntegrationTest extends TestCase
{
    public function testOnRealData()
    {
        $model = new FooModel;
        $resource = new \StdClass;
        $resource->title = 'CREATED';
        $model->createFromObject($resource);

        $model->create();
        $id = $model->getProperty('_id');
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
        $this->assertEquals($id, $model->getProperty('_id'));
        $this->assertEquals('CREATED', $model->getProperty('title'));

        $model->setProperty('title', 'UPDATED');
        $model->update();

        $model = new FooModel;
        $model->one($id);
        $this->assertEquals($id, $model->getProperty('_id'));
        $this->assertEquals('UPDATED', $model->getProperty('title'));

        $model->delete();
        $this->assertTrue(empty(get_object_vars($model->getResource())));

        try {
            $model = new FooModel;
            $model->one($id);
        } catch (RestException $ex) {
            $this->assertEquals('Resource does not exist', $ex->getMessage());
        }
    }
}
