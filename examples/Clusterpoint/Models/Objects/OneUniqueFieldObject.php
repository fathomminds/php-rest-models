<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\OneUniqueFieldSchema;

class OneUniqueFieldObject extends RestObject
{
    protected $schemaClass = OneUniqueFieldSchema::class;
    protected $resourceName = 'one_uf';
}
