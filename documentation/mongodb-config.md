## MongoDB configuration ##

Create the following environment variables in your .env file:

```
MONGODB_HOST=
MONGODB_DATABASE=
MONGODB_USERNAME=
MONGODB_PASSWORD=
```

Example .evn configuration: [.env-example](../.env-example)


### Database setup ###

To use the Models with the MongoDB configuration, the database collections have to be set up correctly. Creation of the database objects are not automatic.

* Follow the MongoDB documentation: [https://docs.mongodb.com/manual/](https://docs.mongodb.com/manual/)

The database name created in the MongoDB console or GUI must match with the `MONGODB_DATABASE` environment variable.

Using the console or GUI, you must create the required collections within your database. The name of the collections must match with the `$resourceName` property of your corresponding RestObjects.

```php
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\MongoDB\RestObject;
use YourApp\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
}

```

To use FooObject with MongoDB you need to create the `foo` collection.

The default primary key value in the RestObjects is `_id`. You can override it by providing the $primaryKey in the FooObject:

```php
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\MongoDB\RestObject;
use Fathomminds\Rest\Examples\MongoDB\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
    protected $primaryKey = 'otherKeyName';
}

```

The primary key name must match the primary key you specified when creating the collection in the MongoDB console or GUI.
