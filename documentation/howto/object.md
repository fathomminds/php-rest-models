## How to create a REST Object? ##

The REST Objects implement the Database operations to read and write the data. Objects implement the [IRestObject](../../src/Contracts/IRestObject.php) interface. You simply extend the correct base class, and specify the properties required for database operations:

### Create a Clusterpoint REST Object ###

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

### Create a DynamoDb REST Object ###

```
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\DynamoDb\RestObject;
use YourApp\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
}

```

### Configure properties ###

```
(string) protected $schemaClass; Name of the Schema class to be used with the object.
(string) protected $resourceName; Table or collection name.
(string) protected $primaryKey; Name of the primary key field. Default value is '_id'.
(array) protected $indexNames; Available indexes in format [(string) fieldName => (string) indexName]; Default is empty array.
