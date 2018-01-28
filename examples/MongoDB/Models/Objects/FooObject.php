<?php
namespace Fathomminds\Rest\Examples\MongoDB\Models\Objects;

use Fathomminds\Rest\Database\MongoDB\RestObject;
use Fathomminds\Rest\Examples\MongoDB\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
}
