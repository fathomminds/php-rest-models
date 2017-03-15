<?php
namespace Fathomminds\Clurexid\Rest\Examples\Models\Objects;

use Fathomminds\Clurexid\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Clurexid\Rest\Examples\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'todo_items';
}
