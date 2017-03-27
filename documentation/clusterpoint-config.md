## Clusterpoint configuration ##

Create the following environment variables in your .env file:

```
CLUSTERPOINT_HOST=
CLUSTERPOINT_ACCOUNT_ID=
CLUSTERPOINT_DATABASE=
CLUSTERPOINT_USERNAME=
CLUSTERPOINT_PASSWORD=
CLUSTERPOINT_DEBUG_MODE=false
```

Example .evn configuration: [.env-example](../.env-example)

Create a `clusterpoint.php` file in the root of your project. See example file: [clusterpoint-example.php](../clusterpoint-example.php)

Clusterpoint PHP documentation: [https://www.clusterpoint.com/docs/api/4/php/345](https://www.clusterpoint.com/docs/api/4/php/345)

### Database setup ###

To use the Models with the Clusterpoint configuration, the databse tables (collections) have to be set up correctly. Creation of the database objects are not automatic.

* Follow the Clusterpoint documentation: [https://www.clusterpoint.com/docs/4.0/21/cloud-account-setup](https://www.clusterpoint.com/docs/4.0/21/cloud-account-setup)

The database name created in the Clusterpoint console must match with the `CLUSTERPOINT_DATABASE` environment variable.

Using the Clusterpoint console, you must create the required collections within your database. The name of the collections must match with the `$resourceName` property of your corresponding RestObjects.

```php
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

To use FooObject with Clusterpoint you need to create the `foo` collection.

The default primary key value in the RestObjects is `_id`. You can override it by providing the $primaryKey in the FooObject:

```php
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
    protected $primaryKey = 'otherKeyName';
}

```

The primary key name must match the primary key you specified when creating the collection in the Clusterpoint console.
