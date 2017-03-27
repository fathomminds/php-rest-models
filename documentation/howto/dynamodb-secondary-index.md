## How to create and use a DynamoDb secondary index? ##

For better application performance you may need to create secondary indexes in DynamoDb. Once the secondary index is created on the DynamoDb table, you need to specify the index nme in the RestObject configuration.

When the index name is available, the query constructor may be able to use an \\Aws\\DynamoDb\\DynamoDbClient::query() instead of a \\Aws\\DynamoDb\\DynamoDbClient::scan()

If you set a (non primary key) schema field to be unique, it is best if you create a secondary index, so instead of scanning the full table for matching values, only the index will be queried.  

### Further read on DynamoDb Scan vs Query ###

* [http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Query.html](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Query.html)
* [http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Scan.html](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Scan.html)
* [http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/SecondaryIndexes.html](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/SecondaryIndexes.html)

### Configuration ###

1. Create a secondary index in the DynamoDb console
2. Add the newly created index to the `$indexName` property of your RestObject

### Example code ###

In this example

* FooSchema is extended with the `title` field
* a secondary index with the name `title-index` has been created on the DynamoDb table for the FooObject.

```php
<?php
namespace YourApp\Models\Schema;

use Fathomminds\Rest\Schema;
use Fathomminds\Rest\Schema\TypeValidators\StringValidator;

/**
 *
 * @property string $_id
 * @property string $title
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
            'title' => [
                'unique' => true,
                'validator' => [
                    'class' => StringValidator::class,
                ]
            ],
        ];
    }
}
```

```php
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\DynamoDb\RestObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
    protected $indexNames = [
        'title' => 'title-index'
    ];
}
```

### DynamoDb Limitations ###

There are certain DynamoDb limitations that you need to be aware of:

* [http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Limits.html#limits-secondary-indexes](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/Limits.html#limits-secondary-indexes)
