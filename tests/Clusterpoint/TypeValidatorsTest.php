<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Fathomminds\Rest\Schema\TypeValidators\ValidatorFactory;
use Fathomminds\Rest\Schema\TypeValidators\AnyValidator;
use Fathomminds\Rest\Schema\TypeValidators\ArrayValidator;
use Fathomminds\Rest\Schema\TypeValidators\DoubleValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;
use Fathomminds\Rest\Schema\TypeValidators\NumberTypeValidator;
use Fathomminds\Rest\Schema\TypeValidators\ObjectValidator;
use Fathomminds\Rest\Schema\TypeValidators\StdTypeValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Helpers\ReflectionHelper;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\BarSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FlopSchema;
use Fathomminds\Rest\Schema\SchemaValidator;

class TypeValidatorsTest extends TestCase
{
    public function testIncorrectValidatorClass()
    {
        $reflectionHelper = new ReflectionHelper;
        $method = $reflectionHelper->createMethod(ValidatorFactory::class, 'getValidatorClass');
        $method->setAccessible(true);
        try {
            $method->invokeArgs(new ValidatorFactory, [['validator'=>['class'=>'INVALID']]]);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Class INVALID does not exist', $ex->getMessage());
        }
    }

    public function testAnyValidator()
    {
        $validator = new AnyValidator;
        try {
            $validator->validate(null);
            $this->assertEquals(1, 1); //Should always reach this line
        } catch (RestException $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testArrayValidatorCorrectTypes()
    {
        $params = [
            'key' => [
                'validator' => [
                    'class' => StringValidator::class,
                ],
            ],
            'item' => [
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
        ];
        $validator = new ArrayValidator($params);
        try {
            $validator->validate(['string'=>'string']);
            $this->assertEquals(1, 1); //Should always reach this line
        } catch (RestException $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testArrayValidatorIncorrectKeyType()
    {
        $params = [
            'key' => [
                'validator' => [
                    'class' => StringValidator::class,
                ],
            ],
            'item' => [
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
        ];
        $validator = new ArrayValidator($params);
        try {
            $validator->validate([1=>'string']);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Array validation failed', $ex->getMessage());
            $this->assertArrayHasKey('keyErrors', $ex->getDetails());
        }
    }

    public function testArrayValidatorIncorrectItemType()
    {
        $params = [
            'key' => [
                'validator' => [
                    'class' => StringValidator::class,
                ],
            ],
            'item' => [
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
        ];
        $validator = new ArrayValidator($params);
        try {
            $validator->validate(['string'=>1]);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Array validation failed', $ex->getMessage());
            $this->assertArrayHasKey('itemErrors', $ex->getDetails());
        }
    }

    public function testFloatValidator()
    {
        $validator = new DoubleValidator;
        try {
            $validator->validate(1.1);
            $this->assertEquals(1, 1); //Should always reach this line
        } catch (RestException $ex) {
            $this->fail();
        }
        try {
            $validator->validate(1);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
            $this->assertArrayHasKey('incorrectType', $ex->getDetails());
            $this->assertEquals('integer', $ex->getDetails()['incorrectType']);
            $this->assertArrayHasKey('correctType', $ex->getDetails());
            $this->assertEquals('double', $ex->getDetails()['correctType']);
        }
    }

    public function testNumberTypeValidator()
    {
        $params = [
            'min'=>0,
            'max'=>10,
        ];
        $validator = new IntegerValidator($params);
        try {
            $validator->validate(-1);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Minimum value error', $ex->getMessage());
        }
        try {
            $validator->validate(11);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Maximum value error', $ex->getMessage());
        }
    }

    public function testObjectTypeValidator()
    {
        $validator = new ObjectValidator;
        try {
            $validator->validate(-1);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
        }
        try {
            $validator->validate(new FooSchema);
            $this->assertEquals(1, 1); //Should always reach this line
        } catch (RestException $ex) {
            $this->fail();
        }
    }

    public function testStringValidatorMaximumLength()
    {
        $params = [
            'maxLength'=>1,
        ];
        $validator = new StringValidator($params);
        try {
            $validator->validate('AB');
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Maximum length error', $ex->getMessage());
        }
    }

    public function testNestedSchemaValidator()
    {
        $flop = new FlopSchema;
        $flop->_id = 'FlopId';
        $flop->mobile = '0011223334445';

        $bar = new BarSchema;
        $bar->_id = 'BarId';
        $bar->flop = $flop;

        $foo = new FooSchema;
        $foo->_id = 'FooId';
        $foo->title = 'FooTitle';
        $foo->status = 1;
        $foo->bar = [
            $bar,
        ];

        $validator = new SchemaValidator;
        try {
            $validator->validate($foo);
            $this->assertEquals(1, 1); //Should always reach this line
        } catch (RestException $ex) {
            $this->fail(); //Should not throw exception
        }
    }

    public function testNestedSchemaValidatorIncorrectNestedRestSchema()
    {
        $bar = new BarSchema;
        $bar->_id = 'BarId';
        $bar->flop = new \StdClass;

        $foo = new FooSchema;
        $foo->_id = 'FooId';
        $foo->title = 'TITLE';
        $foo->status = 1;
        $foo->bar = [
            $bar,
        ];

        $validator = new SchemaValidator;
        try {
            $validator->validate($foo);
            $this->assertEquals(1, 0); // Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Invalid structure', $ex->getMessage());
        }
    }

    public function testNestedSchemaValidatorIncorrectNestedRestSchemaType()
    {
        $flop = new FlopSchema;
        $flop->_id = 'FlopId';
        $flop->mobile = '0011223334445';

        $foo = new FooSchema;
        $foo->_id = 'FooId';
        $foo->title = 'TITLE';
        $foo->status = 1;
        $foo->bar = [
            $flop,
        ];

        $validator = new SchemaValidator;
        try {
            $validator->validate($foo);
            $this->assertEquals(1, 0); // Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Invalid structure', $ex->getMessage());
        }
    }
}
