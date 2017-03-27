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

### Default values ###

You can set constant and run-time generated values too as default values.

```php
<?php
namespace YourApp\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 *
 * @property string $_id
 * @property string $otherField
 *
 */

class FooSchema extends Schema
{
    public function schema()
    {
        return [
            '_id' => [
                'unique' => true,
                'default' => 'STRING',
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
            'otherField' => [
                'default' => function () {
                    return time();
                },
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
        ];
    }
}

```

### Type validators ###

* [AnyValidator](../../src/Schema/TypeValidators/AnyValidator.php)
* [ArrayValidator](../../src/Schema/TypeValidators/ArrayValidator.php)
* [DoubleValidator](../../src/Schema/TypeValidators/DoubleValidator.php)
* [IntegerValidator](../../src/Schema/TypeValidators/IntegerValidator.php)
* [ObjectValidator](../../src/Schema/TypeValidators/ObjectValidator.php)
* [StringValidator](../../src/Schema/TypeValidators/StringValidator.php)

Params set in the field declaration for the validator will be passed to the Validator constructor:

```
Validator::__construct($params);
```

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
                    'params' => [
                        'maxLength' => 16,
                    ],
                ]
            ],
        ];
    }
}

```

### Writing new validators or extending existing ones ###

Type validators must implement the interface [ITypeValidator](../../src/Contracts/ITypeValidator.php).

On validation failure, the validator must throw an exception.

```php
<?php
namespace YourApp\Models\Schema\TypeValidators;

use Fathomminds\Rest\Schema\TypeValidators\StdTypeValidator;
use Fathomminds\Rest\Exceptions\RestException;

class PhoneNumberValidator extends StdTypeValidator
{
    protected $validType = 'string';

    public function validate($value)
    {
        $this->validateType($value);
        $this->validateSomething($value);
    }

    public function validateSomething($value) {
        $somethingIsWrong = true; // Something is always wrong :-)
        if ($somethingIsWrong) {
            throw new RestException(
                'Something is wrong...',
                []
            );
        }
    }
}

```

### Enable IDE autocompletion ###

To enable IDE autocompletion, list the properties. [How to enable IDE autocompletion?](./ide-autocompletion.md)
