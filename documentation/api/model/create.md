# Model API #

## Model::create() ##

Stores the model's resource in database.

### Parameters ###

__None__

### Return ###

Model

### Example code ###

```php
$resource = new \StdClass;
$resource->title = 'TITLE';
$model = new FooModel();
$model->use($resource);
$model->create();

/**
 * Model::resource() returns the resource
 * The properties are configured in the corresponding Schema class
 */
$newId = $model->resource()->_id;

```
