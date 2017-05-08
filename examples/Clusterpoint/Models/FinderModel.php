<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FinderObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FinderSchema;

/**
 *
 * @method FinderSchema resource()
 *
 */

class FinderModel extends RestModel
{
    protected $restObjectClass = FinderObject::class;
}
