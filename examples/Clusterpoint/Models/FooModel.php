<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FooObject;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Schema\FooSchema;

/**
 *
 * @method FooSchema resource()
 *
 */
class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;
}
