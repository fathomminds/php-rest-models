<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FinderSchema;

class FinderObject extends RestObject
{
    protected $schemaClass = FinderSchema::class;
    protected $resourceName = 'finder';
}
