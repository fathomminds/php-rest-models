<?php
namespace Fathomminds\Clurexid\Rest\Examples\Models\Objects;

use Fathomminds\Clurexid\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Clurexid\Rest\Examples\Models\Schema\NoUniqueFieldSchema;

class NoUniqueFieldObject extends RestObject
{
    protected $schemaClass = NoUniqueFieldSchema::class;
    protected $resourceName = 'dummy';
}
