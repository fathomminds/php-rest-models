## Model::use($resource) ##

Sets the Model's resource

### Parameters ###

*(object)* $resource

### Returns ###

*(Model)* The model instance

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
            $resource = new \StdClass;
            $resource->title = 'TITLE';
            $model = new FooModel();
            $model->use($resource);
            $title = $model->resource()->title;
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
