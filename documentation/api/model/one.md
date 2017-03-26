## Model::one($resourceId) ##

Retreives a single resource from the database

### Parameters ###

*(mixed)* $resourceId

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
            $model = new FooModel;
            $model->one('KEY');
            $title = $model->resource()->title;
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
