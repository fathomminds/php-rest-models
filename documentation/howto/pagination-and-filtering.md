## How to do simple pagination and filtering? ##

To implement a simple pagination and filtering for a collection, you can use the [Finder](../api/finder/index.md) class, specifying the size of the page with [Finder::limit($limit)](../api/finder/limit.md) and the start offset with [Finder::limit($limit)](../api/finder/offset.md).

Example code to list the 3rd page, with a page size of 10:

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
