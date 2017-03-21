<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'todo_items';
}
