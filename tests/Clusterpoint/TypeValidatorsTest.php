<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Fathomminds\Rest\Schema\TypeValidators\BooleanValidator;
use Fathomminds\Rest\Schema\TypeValidators\EnumValidator;
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

    public function testArrayValidatorIfNullableOk()
    {
        $params = [
            'nullable' => true,
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
            $validator->validate(null);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Array validator should accept `null` value');
        }
    }

    public function testArrayValidatorIfNullableFail()
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
            $validator->validate(null);
            $this->fail('Array validator should accept not `null` value');
        } catch (RestException $ex) {
            $this->assertEquals(1, 1);
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

    public function testFloatOrNullValidator()
    {
        $validator = new DoubleValidator([
            'nullable' => true,
        ]);
        try {
            $validator->validate(null);
            $this->assertEquals(1, 1); //Should always reach this line
        } catch (RestException $ex) {
            $this->fail();
        }
        $validator = new DoubleValidator();
        try {
            $validator->validate(null);
            $this->assertEquals(1, 0); //Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
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

    public function testNumberTypeOrNullValidator()
    {
        $params = [
            'nullable' => true,
        ];
        $validator = new IntegerValidator($params);
        try {
            $validator->validate(null);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Should accept `null` value');
        }
        $validator = new IntegerValidator();
        try {
            $validator->validate(null);
            $this->assertEquals(1, 0); // Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
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
        $validator = new StringValidator([
            'nullable' => true,
            'maxLength' => 1,
        ]);
        try {
            $validator->validate(null);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Should accept `null` value');
        }
        try {
            $validator->validate('1');
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Should accept `null` value');
        }
        try {
            $validator->validate('11');
            $this->fail('Should not accept `11` string value');
        } catch (RestException $ex) {
            $this->assertEquals(1, 1);
        }
        $validator = new StringValidator([
            'maxLength' => 1,
        ]);
        try {
            $validator->validate(null);
            $this->fail('Should not accept `null` value');
        } catch (RestException $ex) {
            $this->assertEquals(1, 1);
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

    public function testBooleanValidator()
    {
        $validator = new BooleanValidator();
        try {
            $validator->validate(true);
            $validator->validate(false);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail(['Should accept bool values']);
        }
        try {
            $validator->validate('true');
            $this->fail(['Should not accept `true` string value']);
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
        }
        try {
            $validator->validate(null);
            $this->fail(['Should not accept `true` string value']);
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
        }
        try {
            $validator->validate(0);
            $this->fail(['Should not accept `0` int value']);
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
        }
        try {
            $validator->validate(0);
            $this->fail(['Should not accept `1` int value']);
        } catch (RestException $ex) {
            $this->assertEquals('Type mismatch', $ex->getMessage());
        }
        $validator = new BooleanValidator([
            'nullable' => true,
        ]);
        try {
            $validator->validate(null);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail(['Should accept `null` values']);
        }
    }

    public function testEnumValidator()
    {
        $validator = new EnumValidator();
        try {
            $validator->validate(true);
            $this->fail('Should not except any value');
        } catch (RestException $ex) {
            $this->assertEquals('Value is not in valid value list', $ex->getMessage());
        }
        try {
            $validator->validate(null);
            $this->fail('Should not except any value');
        } catch (RestException $ex) {
            $this->assertEquals('Value is not in valid value list', $ex->getMessage());
        }
        $validator = new EnumValidator([
            'nullable' => true,
        ]);
        try {
            $validator->validate(true);
            $this->fail('Should not except `true` boolean value');
        } catch (RestException $ex) {
            $this->assertEquals('Value is not in valid value list', $ex->getMessage());
        }
        try {
            $validator->validate(null);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Should except `null` value');
        }
        $validator = new EnumValidator([
            'validValues' => [0, 10, 15, 20],
        ]);
        try {
            $validator->validate(3);
            $this->fail('Should not except `3` int value');
        } catch (RestException $ex) {
            $this->assertEquals('Value is not in valid value list', $ex->getMessage());
        }
        try {
            $validator->validate(null);
            $this->fail('Should not accept `null` value');
        } catch (RestException $ex) {
            $this->assertEquals('Value is not in valid value list', $ex->getMessage());
        }
        try {
            $validator->validate(10);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Should accept `10` int value');
        }
        $validator = new EnumValidator([
            'nullable' => true,
            'validValues' => [0, 10, 15, 20],
        ]);
        try {
            $validator->validate(3);
            $this->fail('Should not except `3` int value');
        } catch (RestException $ex) {
            $this->assertEquals('Value is not in valid value list', $ex->getMessage());
        }
        try {
            $validator->validate(null);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Should accept `null` value');
        }
        try {
            $validator->validate(10);
            $this->assertEquals(1, 1);
        } catch (RestException $ex) {
            $this->fail('Should accept `10` int value');
        }
    }
}
