## Model::replace() ##

Replaces the model's resource in database.

### Parameters ###

*None*

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
            $model->resource()->title = 'NEW TITLE';
            $model->replace();
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
