## How to create a schema? ##

Create a schema by extending your custom schema class from ShcemaValidator:

```
<?php
namespace YourApp\Models\Schema;

use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

class FooSchema extends SchemaValidator
{
    protected $fields = [
        '_id' => [
            'unique' => true,
            'validator' => [
                'class' => StringValidator::class,
            ]
        ],
    ];
}

```

Define the schema by adding all fields. Fields are defined as an associative array in the $fields class property. The following properties can be set:

```
(bool) unique = true|false
(bool) required = true|false
(mixed) default = a constant value - This declaration may include an initialization, but this initialization must be a constant value--that is, it must be able to be evaluated at compile time and must not depend on run-time information in order to be evaluated. [(http://de2.php.net/manual/en/language.oop5.properties.php)](http://de2.php.net/manual/en/language.oop5.properties.php)
(array) validator = [
      (string) 'class' => (string) className, // the name of the class that implements the validation,
      (array) 'params' => [
          (string) 'key1' => (mixed) value1,
          (string) 'key2' => (mixed) value2,
          // ...
      ], // passed to the Validator::__construct($params)
  ]; // the validator to be used to validate this field's value
```

Setting a value generator function as default field value (e.g. genrating a UUID run time):

```
<?php
namespace YourApp\Models\Schema;

use Fathomminds\Rest\Schema\SchemaValidator;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;
use Fathomminds\Rest\Helpers\Uuid;

class FooSchema extends SchemaValidator
{
    protected $fields = [
        '_id' => [
            'unique' => true,
            'validator' => [
                'class' => StringValidator::class,
            ]
        ],
    ];

    public function __construct()
    {
        $this->setDefault('_id', function () {
            return (new Uuid)->generate();
        });
    }
}
```

### Type validators ###

* [AnyValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [ArrayValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [DoubleValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [IntegerValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [ObjectValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [StringValidator](../../src/Schema/TypeValidators/AnyValidator.php)

### Writing new validators or extending existing ones ###

Type validators must implement the interface [ITypeValidator](../../src/Contracts/ITypeValidator.php).

On validation failure, the validator must throw an exception.
