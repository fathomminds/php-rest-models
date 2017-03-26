## How to create a Model? ##

The Model will implement the application specific business logic. You only need to specify which REST Object to use for storing and retreiving data. If you need to use complex queries, you can get a database client with Model::getClient() and write the queries directly against the database.

### Create a Model with a Clusterpoint REST Object ###

```php
<?php
namespace YourApp\Models;

use Fathomminds\Rest\RestModel;
use YourApp\Models\Objects\FooObject;

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

### Create a Model with a DynamoDb REST Object ###

```php
<?php
namespace YourApp\Models;

use Fathomminds\Rest\RestModel;
use YourApp\Models\Objects\FooObject;

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

### Configure properties ###

```
(string) protected $restObjectClass; Name of the REST Object class to be used with the Model.
```

### Enable IDE autocompletion ###

To enable IDE autocompletion, specify the return type for the inherited resource() method
