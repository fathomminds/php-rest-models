## Finder::where($whereConditions) ##

Sets the where conditions that should be used in the find query.

### Parameters ###

*(string)* $whereConditions The where conditions that should be used in the find query

### Returns ###

*(Finder)* The finder instance

### Where conditions ###

The where conditions is an array, that contains the conditions as arrays with the following signature: `[FIELDNAME, OPERATOR, VALUE]`.

Operators available are: `==, !=, <, >, <=, >=`

More complex find queries can be constructed by grouping conditions with logical operators: `AND, OR`. The default logical operator on the root level of the where condition array is `AND`.

*Note:* Aim of the Finder is to provide a simple interface to find documents in collections. If very complex queries are required, use the database APIs to achieve the best possible performance.

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

### Example use with a complex where condition ###

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
                'AND' => [
                    ['F1', '==', 'V1'],
                    ['F2', '==', 'V2'],
                ],
                'OR' => [
                    ['F3', '==', 'V3'],
                    ['F4', '==', 'V4'],
                ],
                ['F5', '==', 'V5'],
            ])
            ->orderBy('status', 'DESC')
            ->limit(10)
            ->offset(20)
            ->get()
            ->all();
    }
}

```
