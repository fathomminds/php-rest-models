## How to execute custom queries? ##

To execute custom queries, you can simply use the database APIs, and use the retrieved documents with the Model API. You can grab an instance of the database API easily with the [Model::query()](../api/model/query.md) method. The model's RestObject's underlying database API will be returned/

Example code to grab a database API via [Model::query()](../api/model/query.md) and use the database API to execute queries of any complexity:

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
