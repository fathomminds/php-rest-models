## Model::all() ##

Retreives multiple resources from the database

### Parameters ###

*None*

### Returns ###

*(Array<SchemaClass>)* An array of resources with the SchemaClass configured in the Model's RestObject or an empty array if none found

### Throws ###

*RestException*

### Example code ###

```php
<?php
namespace YourApp\Logic;

use Fathomminds\Rest\Exceptions\RestException;
use YourApp\Models\FooModel;

class Do
{
    public function something()
    {
        try {
            $model = new FooModel;
            $list = $model->all();
            foreach ($list as $resource) {
                $model = new FooModel;
                $model->use($resource);
            }
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
