<?php
namespace Fathomminds\Clurexid\Rest\Tests;

use Fathomminds\Clurexid\Rest\Schema\SchemaValidator;
use Fathomminds\Clurexid\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Clurexid\Rest\Examples\Models\Schema\FooSchema;
use Fathomminds\Clurexid\Rest\Examples\Models\Objects\FooObject;
use Fathomminds\Clurexid\Rest\Examples\Models\FooModel;
use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;
use Fathomminds\Clurexid\Rest\Helpers\ReflectionHelper;

class SchemaValidatorTest extends TestCase
{
    public function testIncorrectSchemaException()
    {
        $foo = $this->mockModel(FooModel::class, FooObject::class);
        $this->expectException(DetailedException::class);
        $foo->validate();
    }

    public function testMissingRequiredField()
    {
        try {
            $foo = $this->mockModel(FooModel::class, FooObject::class);
            $foo->validate();
        } catch (DetailedException $ex) {
            $error = $ex->getMessage();
            $details = $ex->getDetails();
            $this->assertEquals('Invalid structure', $error);
            $this->assertArrayHasKey('schema', $details);
            $this->assertEquals(FooSchema::class, $details['schema']);
            $this->assertArrayHasKey('errors', $details);
            $this->assertArrayHasKey('title', $details['errors']);
            $this->assertEquals('Missing required field', $details['errors']['title']);
        }
    }

    public function testCorrectSchema()
    {
        try {
            $foo = $this->mockModel(FooModel::class, FooObject::class);
            $foo->setProperty('title', 'REQUIRED');
            $foo->validate();
            $this->assertEquals(1, 1); //Reaching this line only if no exception is thrown
        } catch (DetailedException $ex) {
            $this->fail(); //Correct structure should not trigger an exception
        }
    }

    public function testTypeValidatorException()
    {
        try {
            $foo = $this->mockModel(FooModel::class, FooObject::class);
            $foo->setProperty('title', 1);
            $foo->validate();
            $this->assertEquals(1, 1); //Reaching this line only if no exception is thrown
        } catch (DetailedException $ex) {
            $this->assertEquals('Invalid structure', $ex->getMessage());
        }
    }

    public function testObjectExpection()
    {
        $reflectionHelper = new ReflectionHelper;
        $method = $reflectionHelper->createMethod(SchemaValidator::class, 'expectObject');
        $method->setAccessible(true);
        try {
            $method->invokeArgs(new FooSchema, ['string']);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (DetailedException $ex) {
            $this->assertEquals('Object expected', $ex->getMessage());
        }
    }
}
