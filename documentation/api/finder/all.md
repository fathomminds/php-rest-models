## Finder::first() ##

Returns the internally stored result of the executed query as array. If the result set is empty, the call returns an empty `array`.

### Parameters ###

*(none)*

### Returns ###

*(array)* The internally stored result of the executed query

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
