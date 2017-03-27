## DynamoDb configuration ##

Create the following environment variables in your .env file:

```
# AWS credentials
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=

# AWS SDK settings
AWS_SDK_ENDPOINT=
AWS_SDK_REGION=
AWS_SDK_VERSION=
AWS_SDK_HTTP_VERIFY=

# Multi tenancy
# Tables to be created in AWS with the following naming convention [NAMESPACE]-[DATABASE]-[RESOURCENAME]
# [RESOURCENAME] is to be set in the Schema classes
AWS_DYNAMODB_NAMESPACE=
AWS_DYNAMODB_DATABASE=
```

Example .evn configuration: [.env-example](../.env-example)

AWS SDK for PHP documentation: [https://aws.amazon.com/sdk-for-php/](https://aws.amazon.com/sdk-for-php/)

### Database setup ###

To use the Models with the DynamoDb configuration, the databse tables (collections) have to be set up correctly. Creation of the database objects are not automatic.

* AWS credentials
    * Follow the AWS documentation: [https://aws.amazon.com/premiumsupport/knowledge-center/create-access-key/](https://aws.amazon.com/premiumsupport/knowledge-center/create-access-key/)
* AWS SDK settings
    * Follow the AWS documentation: [http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html](http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html)

In the AWS DynamoDb service the highest level entity is a table. Therefore, to provide multitanency (i.e. multiple applications), the table names can be prefixed. This package will use the following naming convention for table names, providing a simple multi tenancy solution:

`Table name = [AWS_DYNAMODB_NAMESPACE]-[AWS_DYNAMODB_DATABASE]-[RESOURCE_NAME]`

The [RESOURCE_NAME] is the `$resourceName` property set in the RestObjects.

```php
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

### Example ###

Setting up a development and a production database, and use the above FooObject in these databases:

#### Development environment ####

```
AWS_DYNAMODB_NAMESPACE=yourNameSpace
AWS_DYNAMODB_DATABASE=development
```

Table to be created in the DynamoDb console: `yourNameSpace-development-foo`

#### Production environment ####

```
AWS_DYNAMODB_NAMESPACE=yourNameSpace
AWS_DYNAMODB_DATABASE=production
```

Table to be created in the DynamoDb console: `yourNameSpace-production-foo`

### Primary Key ###

The default primary key value in the RestObjects is `_id`. You can override it by providing the $primaryKey in the FooObject:

```php
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\DynamoDb\RestObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
    protected $primaryKey = 'otherKeyName';
}

```

The primary key name must match the primary key you specified when creating the collection in the DynamoDb console.

**Note:** currently (version 1.2.x) only partition keys are supported as primary key, a sort key is not handled

#### Further read on AWS DynamoDb keys ####

* [http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/WorkingWithTables.html#WorkingWithTables.primary.key](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/WorkingWithTables.html#WorkingWithTables.primary.key)

* [http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/GuidelinesForTables.html](http://docs.aws.amazon.com/amazondynamodb/latest/developerguide/GuidelinesForTables.html)

### How to use a secondary index? ###

See [How to create and use a DynamoDb secondary index?](../../howto/dynamodb-secondary-index.md)
