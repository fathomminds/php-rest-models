<?php
namespace Fathomminds\Rest\Tests\DynamoDb;

use Mockery;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FooSchema;

class SchemaTest extends TestCase
{
    public function testIncorrectConstructorParameter()
    {
        try {
            $schema = new FooSchema(-1);
            $this->fail();
        } catch (RestException $ex) {
            $this->assertEquals(
                'Schema constructor expects object or null as parameter',
                $ex->getMessage()
            );
        }
    }

    public function testAccessingUndefinedProperty()
    {
        try {
            $schema = new FooSchema;
            $value = $schema->none;
            $this->fail();
        } catch (RestException $ex) {
            $this->assertEquals(
                'Trying to access undefined property ' . 'none',
                $ex->getMessage()
            );
        }
    }

    public function testAccessingDefinedPropertyWithMagicMethod()
    {
        $schema = new FooSchema;
        $schema->field = 'VALUE';
        $value = $schema->__get('field');
        $this->assertEquals('VALUE', $value);
    }
}
