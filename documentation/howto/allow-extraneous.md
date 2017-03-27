## How to allow undefined schema fields? ##

By default the schema validation will fail if the Model's resource has properties that are not defined in the schema. To change this behaviour and allow using undefined fields, you can change the `$allowExtraneous` property in your RestObjects.

### Allowing usage of undefined schema fields  ###

```php
<?php
namespace YourApp\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use YourApp\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
    protected $allowExtraneous = true;
}

```

**Note:** no validation will be executed for undefined fields
