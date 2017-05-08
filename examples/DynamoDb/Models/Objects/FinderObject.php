<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Objects;

use Fathomminds\Rest\Database\DynamoDb\RestObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FinderSchema;

class FinderObject extends RestObject
{
    protected $schemaClass = FinderSchema::class;
    protected $resourceName = 'finder';
}
