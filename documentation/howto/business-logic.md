## How to implement the business logic? ##

In the Model class(es) you can implement the business logic for your application. Models may interact with each other, i.e. a Model may istantiate other Models, or 3rd party integrations, or anything you may require.

Finally, the Model state may change, and you may need to do more complex validations or calculate some of the Model properties when saving or deleting the data.

To implement these additional steps you can override the Model methods.

For example, for performance reasons, you may want to utilize a counter property `barCounter` in FooModel. The counter property should always be set automatically when updating the Model, and it should store how many elements there are in the `bar` property.

### Add the required fields to FooSchema ###

```
<?php
namespace YourApp\Models\Schema;

use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Schema\TypeValidators\ArrayValidator;
use Fathomminds\Rest\Schema\TypeValidators\IntegerValidator;

class FooSchema extends SchemaValidator
{
    protected $fields = [
        '_id' => [
            'unique' => true,
            'validator' => [
                'class' => StringValidator::class,
            ]
        ],
        'bar' => [
            'validator' => [
                'class' => ArrayValidator::class,
            ]
        ],
        'barCounter' => [
            'validator' => [
                'class' => IntegerValidator::class,
            ]
        ],
    ];
}

```

### No change required in the REST Object ###

```
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use YourApp\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
}

```

### Override the Model::update() method to set the counter ###

```
<?php
namespace YourApp\Models;

use Fathomminds\Rest\RestModel;
use YourApp\Models\Objects\FooObject;

class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;

    public function update()
    {
        $this->setProperty('barCounter', count($this->getProperty('bar')));
        parent::update();
    }
}

```
