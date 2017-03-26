## How to create a schema? ##

Create a schema by extending your custom schema class from a base [Schema](../../src/Schema.php) class:

```php
<?php
namespace YourApp\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 *
 * @property string $_id
 *
 */

class FooSchema extends Schema
{
    public function schema()
    {
        return [
            '_id' => [
                'unique' => true,
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
        ];
    }
}

```

Define the schema by adding all fields. Fields are defined as an associative array in the return value of the schema() method. The following properties can be set:

```
(bool) unique = true|false
(bool) required = true|false
(mixed) default = a constant value
(array) validator = [
      (string) 'class' => (string) the name of the class that implements the validation,
      (array) 'params' => [
          (string) 'key1' => (mixed) value1,
          (string) 'key2' => (mixed) value2,
          ...
      ],
  ];
```

### Type validators ###

* [AnyValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [ArrayValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [DoubleValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [IntegerValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [ObjectValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [StringValidator](../../src/Schema/TypeValidators/AnyValidator.php)

Params set in the field declaration for the validator will be passed to the Validator constructor:

```
Validator::__construct($params);
```

### Writing new validators or extending existing ones ###

Type validators must implement the interface [ITypeValidator](../../src/Contracts/ITypeValidator.php).

On validation failure, the validator must throw an exception.
