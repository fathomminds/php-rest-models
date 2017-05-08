<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\DynamoDb\Models\Objects\FinderObject;
use Fathomminds\Rest\Examples\DynamoDb\Models\Schema\FinderSchema;

/**
 *
 * @method FinderSchema resource()
 *
 */

class FinderModel extends RestModel
{
    protected $restObjectClass = FinderObject::class;
}
