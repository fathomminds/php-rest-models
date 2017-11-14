<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models\Objects;

use Fathomminds\Rest\Database\Clusterpoint\RestObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\OnePKSchema;

class OnePKObject extends RestObject
{
    protected $schemaClass = OnePKSchema::class;
    protected $resourceName = 'one_pk';
}
