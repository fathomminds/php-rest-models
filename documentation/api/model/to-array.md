## Model::toArray() ##

Returns the Model's currently set resource object as an associative array

### Parameters ###

*None*

### Returns ###

*(array)* An associative array creted fomr the Model's resource

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
            $array = $model->toArray();
            $title = $array['title'];
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
