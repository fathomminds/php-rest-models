<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\OneUniqueFieldObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\OneUniqueFieldSchema;

/**
 *
 * @method OneUniqueFieldSchema resource($resource = null)
 *
 */
 
class OneUniqueFieldModel extends RestModel
{
    protected $restObjectClass = OneUniqueFieldObject::class;
}
