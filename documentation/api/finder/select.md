## Finder::select($fieldNamesArray) ##

Sets the fields that should be returned.

### Parameters ###

*(Array<string>)* $fieldNamesArray An array that contains the name of the fields to be returned

### Returns ###

*(Finder)* The finder instance

### Example code ###

```php
<?php
namespace YourApp\Logic;

use Fathomminds\Rest\Database\Clusterpoint\Finder;

class Do
{
    public function something()
    {
        $finder = new Finder;
        $items = $finder
            ->database('php_rest_models_integration_test')
            ->select(['title', 'status'])
            ->from('finder')
            ->where([
              ['status', '>', 10],
            ])
            ->orderBy('status', 'DESC')
            ->limit(10)
            ->offset(20)
            ->get()
            ->all();
    }
}

```
