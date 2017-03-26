## How to enable IDE autocompletion? ##

To enable IDE autocompletion you only need to specify the return type for Model::resource() and list the properties in the SchemaClasses:

### Specify return type for Model::resource() ###

```php
<?php
namespace YourApp\Models;

use Fathomminds\Rest\RestModel;
use YourApp\Models\Objects\FooObject;
use YourApp\Models\Schema\FooSchema;

/**
 *
 * @method FooSchema resource()
 *
 */
class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;
}


```

### List the properties in the SchemaClass ###

```php
<?php
namespace YourApp\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;
use Fathomminds\Rest\Helpers\Uuid;
use YourApp\Models\Schema\BarSchema;

/**
 *
 * @property string $_id
 * @property string $title
 * @property integer $status
 * @property BarSchema $bar
 *
 */


class FooSchema extends Schema
{
    public function schema()
    {
        return [
            '_id' => [
                'unique' => true,
                'default' => function () {
                    return (new Uuid)->generate();
                },
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'title' => [
                'unique' => true,
                'required' => true,
                'validator' => [
                    'class' => StringValidator::class,
                    'params' => [
                        'maxLength' => 100,
                    ],
                ],
            ],
            'status' => [
                'default' => 0,
                'validator' => [
                    'class' => IntegerValidator::class,
                    'params' => [
                        'min' => 0,
                        'max' => 1,
                    ],
                ],
            ],
            'bar' => [
                'type' => BarSchema::class,
            ],
        ];
    }
}

```
