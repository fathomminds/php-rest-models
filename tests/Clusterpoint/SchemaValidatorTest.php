<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Mockery;
use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FooObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\FooModel;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Helpers\ReflectionHelper;

class SchemaValidatorTest extends TestCase
{
    public function testIncorrectSchemaException()
    {
        $foo = $this->mockModel(FooModel::class, FooObject::class);
        $this->expectException(RestException::class);
        $foo->validate();
    }

    public function testMissingRequiredField()
    {
        try {
            $foo = $this->mockModel(FooModel::class, FooObject::class);
            $foo->validate();
        } catch (RestException $ex) {
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
            $resource = new FooSchema;
            $resource->title = 'REQUIRED';
            (new SchemaValidator)->validate($resource);
            $this->assertEquals(1, 1); //Reaching this line only if no exception is thrown
        } catch (RestException $ex) {
            $this->fail(); //Correct structure should not throw an exception
        }
    }

    public function testTypeValidatorException()
    {
        try {
            $foo = $this->mockModel(FooModel::class, FooObject::class);
            $foo->resource()->title = 1;
            $foo->validate();
            $this->assertEquals(1, 1); //Reaching this line only if no exception is thrown
        } catch (RestException $ex) {
            $this->assertEquals('Invalid structure', $ex->getMessage());
        }
    }

    public function testObjectExpection()
    {
        $reflectionHelper = new ReflectionHelper;
        $method = $reflectionHelper->createMethod(SchemaValidator::class, 'expectObject');
        $method->setAccessible(true);
        try {
            $method->invokeArgs(new SchemaValidator, ['string']);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Object expected', $ex->getMessage());
        }
    }

    public function testExtraneousFieldNotAllowed()
    {
        try {
            $resource = new FooSchema;
            $resource->title = 'REQUIRED';
            $resource->extraneous = 'exists';
            $schemaValidator = new SchemaValidator;
            $schemaValidator->allowExtraneous(false);
            $schemaValidator->validate($resource);
            $this->fail(); //Reaching this line only if no exception is thrown
        } catch (RestException $ex) {
            $this->assertEquals('Invalid structure', $ex->getMessage());
            $this->assertEquals('Extraneous field', $ex->getDetails()['errors']['extraneous']);
        }
    }

    public function testExtraneousFieldAllowed()
    {
        try {
            $resource = new FooSchema;
            $resource->title = 'REQUIRED';
            $resource->extraneous = 'exists';
            $schemaValidator = new SchemaValidator;
            $schemaValidator->allowExtraneous(true);
            $schemaValidator->validate($resource);
            $this->assertEquals(1, 1); //Reaching this line only if no exception is thrown
        } catch (RestException $ex) {
            $this->fail();
        }
    }

    public function testUpdateModeValidation()
    {
        try {
            $resource = new FooSchema;
            // $resource->title = 'REQUIRED'; // Skipping required field for update mode
            $resource->extraneous = 'exists';
            $schemaValidator = new SchemaValidator;
            $schemaValidator->updateMode(true);
            $schemaValidator->allowExtraneous(true);
            $schemaValidator->validate($resource);
            $this->assertEquals(1, 1); //Reaching this line only if no exception is thrown
        } catch (RestException $ex) {
            $this->fail();
        }
    }
}
