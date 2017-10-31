<?php
namespace Fathomminds\Rest\Tests\Clusterpoint;

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
}
