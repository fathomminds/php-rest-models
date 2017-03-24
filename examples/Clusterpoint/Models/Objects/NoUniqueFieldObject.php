<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\NoUniqueFieldSchema;

class NoUniqueFieldObject extends RestObject
{
    protected $schemaClass = NoUniqueFieldSchema::class;
    protected $resourceName = 'dummy';
}
