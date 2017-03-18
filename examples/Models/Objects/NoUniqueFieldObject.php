<?php
namespace Fathomminds\Rest\Examples\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Rest\Examples\Models\Schema\NoUniqueFieldSchema;

class NoUniqueFieldObject extends RestObject
{
    protected $schemaClass = NoUniqueFieldSchema::class;
    protected $resourceName = 'dummy';
}
