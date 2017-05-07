## Model::find() ##

Returns a Finder to execute simple queries

### Parameters ###

*(none)*

### Returns ###

*(Finder)* A [Finder](../finder/index.md) instance

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
            $resource = $model
                ->find()
                ->where([
                  ['FIELD', '==', 'VALUE']
                ])
                ->get()
                ->first();
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
