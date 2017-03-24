<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Objects;

use Fathomminds\Rest\Database\DynamoDb\RestObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FooSchema;

class FooObject extends RestObject
{
    protected $schemaClass = FooSchema::class;
    protected $resourceName = 'foo';
    protected $indexNames = [
        'title' => 'title-index',
        'other' => 'other-index',
    ];
}
