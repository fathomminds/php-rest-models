<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\CastExample\ZSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FruitSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\InvalidNestedSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\InvalidSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\SeedSchema;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\TreeSchema;
use Fathomminds\Rest\Exceptions\RestException;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;

class SchemaTest extends TestCase
{

    public function testSetFieldDefaults()
    {
        $expectedSchema = FooSchema::cast((object)[
            '_id' => 'ID',
            'title' => 'Title for flip: Default Title',
            'status' => 0,
            'flip' => (object)[
                'name' => 'Default Flip Name',
                'email' => 'flip@flip.hu',
            ],
            'boo' => (object)[
                'title' => 'test',
                'name' => 'Default Boo Name',
                'email' => 'test@test.hu',
            ],
        ]);
        $schema = FooSchema::cast((object)[
            '_id' => 'ID',
            'flip' => (object)[
                'email' => 'flip@flip.hu',
            ],
        ]);
        $schema->setFieldDefaults();
        $this->assertEquals($expectedSchema, $schema);
    }

    public function testValidateFail()
    {
        $expectedExceptionMsg = 'Invalid structure';
        $expectedExceptionDtls = [
            'schema' => 'Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema',
            'errors' => [
                'title' => 'Missing required field'
            ]
        ];
        try {
            $schema = FooSchema::cast((object)[
                '_id' => 'ID',
                'flip' => (object)[
                    'email' => 'flip@flip.hu',
                ],
            ]);
            $schema->validate();
            $this->assertEquals('RestException was expected', 'No exception happened'); // Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals($expectedExceptionMsg, $ex->getMessage());
            $this->assertEquals($expectedExceptionDtls, $ex->getDetails());
        } catch (\Exception $ex) {
            $this->assertEquals('RestException was expected', 'Exception happened'); // Should not reach this line
        }
    }

    public function testValidateOk()
    {
        try {
            $schema = FooSchema::cast((object)[
                '_id' => 'ID',
                'flip' => (object)[
                    'email' => 'flip@flip.hu',
                ],
            ]);
            $schema->setFieldDefaults();
            $schema->validate();
            $this->assertEquals('Validation was successful', 'Validation was successful');
        } catch (\Exception $ex) {
            $this->assertEquals('No exception was expected', 'Exception happened'); // Should not reach this line
        }
    }

    public function testValidateInReplaceModeOk()
    {
        try {
            $schema = FooSchema::cast((object)[
                '_id' => 'ID',
                'flip' => (object)[
                    'email' => 'flip@flip.hu',
                ],
            ]);
            $schema->setFieldDefaults();
            $schema->validate(FooSchema::REPLACE_MODE);
            $this->assertEquals('Validation was successful', 'Validation was successful');
        } catch (\Exception $ex) {
            $this->assertEquals('No exception was expected', 'Exception happened'); // Should not reach this line
        }
    }

    public function testValidateInReplaceModeFail()
    {
        $expectedExceptionMsg = 'Invalid structure';
        $expectedExceptionDtls = [
            'schema' => 'Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema',
            'errors' => [
                'title' => 'Missing required field'
            ]
        ];
        try {
            $schema = FooSchema::cast((object)[
                '_id' => 'ID',
                'flip' => (object)[
                    'email' => 'flip@flip.hu',
                ],
            ]);
            $schema->validate(FooSchema::REPLACE_MODE);
            $this->assertEquals('Exception was expected', 'No exception happened'); // Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals($expectedExceptionMsg, $ex->getMessage());
            $this->assertEquals($expectedExceptionDtls, $ex->getDetails());
        } catch (\Exception $ex) {
            $this->assertEquals('RestException was expected', 'Exception happened'); // Should not reach this line
        }
    }

    public function testValidateInUpdateModeOk()
    {
        try {
            $schema = FooSchema::cast((object)[
                '_id' => 'ID',
                'flip' => (object)[
                    'email' => 'flip@flip.hu',
                ],
            ]);
            // $schema->setFieldDefaults(); // Should skip required filed check in update mode
            $schema->validate(FooSchema::UPDATE_MODE);
            $this->assertEquals('Validation was successful', 'Validation was successful');
        } catch (\Exception $ex) {
            $this->assertEquals('No exception was expected', 'Exception happened'); // Should not reach this line
        }
    }

    public function testCircularDependency()
    {
        try {
            TreeSchema::cast((object)[])->validate();
            $this->assertEquals('Exception was expected', 'No exception happened'); // Should not reach this line
        } catch (RestException $ex) {
            $this->assertEquals('Circular dependency found in schema definition', $ex->getMessage());
            $this->assertEquals([
                'schema' => TreeSchema::class,
                'chain' => [
                    FruitSchema::class,
                    SeedSchema::class,
                    TreeSchema::class,
                ],
            ], $ex->getDetails());
        } catch (\Exception $ex) {
            $this->assertEquals('RestException was expected', 'Exception happened'); // Should not reach this line
        }
    }

    public function testCastByMap()
    {
        $expectedSchema = ZSchema::cast((object)[
            'a' => 12,
            'b' => (object)[
                'd' => null,
                'e' => 12,
            ],
        ]);
        $castedSchema = ZSchema::castByMap(
            (object)[
                'a' => (object)[
                    'e' => 12,
                    'h' => '34',

                ],
                'b' => (object)[
                    'g' => (object)[
                        'i' => 12,
                        'j' => null,
                        'x' => null,
                    ],
                ],
                'f' => (object)[
                    'k' => 354.125,
                ],
            ],
            [
                'a' => 'a.e',
                'b.d' => 'b.g.j',
                'b.e' => 'b.g.i',
                'c' => 'f.g',
                'd' => 'f.k',
            ]
        );
        $this->assertEquals($expectedSchema, $castedSchema);
    }

    public function testWithoutExtraneous()
    {
        $expectedSchema = ZSchema::cast((object)[
            'a' => 12,
            'b' => (object)[
                'd' => 12,
                'e' => null,
            ],
            'c' => 'string',
        ]);
        $castedSchema = ZSchema::castWithoutExtraneous(
            (object)[
                'a' => 12,
                'b' => (object)[
                    'd' => 12,
                    'e' => null,
                    'x' => null,
                ],
                'c' => 'string',
                'd' => 'string2'
            ]
        );
        $this->assertEquals($expectedSchema, $castedSchema);
    }

    public function testWithoutExtraneousNonObject()
    {
        try {
            ZSchema::castWithoutExtraneous(null);
            $this->fail('Should not accept input parameter `object`');
        } catch (\Exception $ex) {
            $this->assertEquals(
                'Schema castWithoutExtraneous method expects object as parameter',
                $ex->getMessage()
            );
        }
    }

    public function testWithoutByMapNonObject()
    {
        try {
            ZSchema::castByMap(null, []);
            $this->fail('Should not accept input parameter `object`');
        } catch (\Exception $ex) {
            $this->assertEquals(
                'Schema castByMap method expects object as first parameter',
                $ex->getMessage()
            );
        }
    }

    public function testWithoutByMapNonArray()
    {
        try {
            ZSchema::castByMap((object)[], null);
            $this->fail('Should not accept input parameter `map`');
        } catch (\Exception $ex) {
            $this->assertEquals(
                'Schema castByMap method expects array as second parameter',
                $ex->getMessage()
            );
        }
    }
}
