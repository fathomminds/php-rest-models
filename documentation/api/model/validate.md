## Model::validate() ##

Executes the Model validation. The validation checks the correctness of the resource shape and checks the unique fields. By overriding the inherited validate() method it is possible to extend or rewrite the validation logic. In case of extending the validation (as opposed to completely rewriting), the inherited validation logic can be called with `parent::validate();`  

Before Model::create() and Model::update() the Model::validate() method is called. To change this behaviour the Model::create() and Model::update() methods may be overwritten.

### Parameters ###

*None*

### Returns ###

*void* The method does not return value

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
            $model = new FooModel();
            $model->use($resource);
            $model->validate();
        } catch (RestException $exception) {
            $message = $exception->getMessage();
            $details = $exception->getDetails();
        }
    }
}

```
