## Model::create() ##

Stores the model's resource in database.

### Parameters ###

*None*

### Returns ###

*(Model)* $model

### Throws ###

*RestException* $exception

### Example code ###

```php
<?php
namespace YourApp\Logic;

use Fathomminds\Rest\Exceptions\RestException
use YourApp\Models\FooModel;

class Do
{
    public function something()
    {
        $resource = new \StdClass;
        $resource->title = 'TITLE';
        $model = new FooModel();
        $model->use($resource);
        try {
            $model->create();
            $newId = $model->resource()->_id;
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
