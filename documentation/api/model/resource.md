## Model::resource() ##

Returns the Model's currently set resource object

### Parameters ###

*None*

### Returns ###

*(object)* Returns a reference to an instance of an object of the SchemaClass configured

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
            $model = new FooModel();
            $model->one('KEY');
            $title = $model->resource()->title;
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
