## Model::delete() ##

Removes the model's resource (identified by its primary key) from the database

### Parameters ###

*None*

### Returns ###

*(mixed)* The primary key value of the deleted item

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
            $model->one('KEY')->delete();
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
