<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Objects;

use Fathomminds\Rest\Database\DynamoDb\RestObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\NoUniqueFieldSchema;

class NoUniqueFieldObject extends RestObject
{
    protected $schemaClass = NoUniqueFieldSchema::class;
    protected $resourceName = 'dummy';
}
