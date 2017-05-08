## Model::find() ##

Returns an instance to an object that can be used to execute queries directly against the database. I.e. the database API provided by the database vendor.

### Parameters ###

*(none)*

### Returns ###

*(mixed)* An instance of the database API

### Example code (Clusterpoint API) ###

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
            $resources = $model
                ->query()                     // Returns Clusterpoint API
                ->orWhere('F1', '==', 'V1')   // Using Clusterpoint API
                ->orWhere('F1', '==', 'V2')   // Using Clusterpoint API
                ->get()                       // Using Clusterpoint API
                ->toArray();                  // Using Clusterpoint API
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
