<?php
namespace Fathomminds\Clurexid\Rest\Tests;

use Fathomminds\Clurexid\Rest\Examples\Models\Objects\NoUniqueFieldObject;

class RestObjectTest extends TestCase
{
    public function testNoUniqueFieldSchemaValidation()
    {
        $object = $this->mockObject(NoUniqueFieldObject::class);
        $input = new \StdClass();
        $input->_id = 'ID';
        $input->multi = 'MULTI';
        $object = $object->createFromObject($input);
        $object->validateUniqueFields(); // Trigger early return in RestObject::validateUniqueFields
        $this->assertCount(0, $object->getUniqueFields());
    }
}
