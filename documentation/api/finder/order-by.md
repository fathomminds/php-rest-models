## Finder::orderBy($fieldName, $sortMode = 'ASC') ##

Sets the order clause that should be applied to the find query.

### Parameters ###

*(string)* $fieldName The name of the field used in the orderBy clause

*(string)[ASC|DESC]* $sortMode The sort mode used in the orderBy clause. Default is ASC.

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
