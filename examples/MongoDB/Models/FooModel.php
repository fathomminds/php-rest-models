<?php
namespace Fathomminds\Rest\Examples\MongoDB\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\MongoDB\Models\Objects\FooObject;
use Fathomminds\Rest\Examples\MongoDB\Models\Schema\FooSchema;

/**
 *
 * @method FooSchema resource($resource = null)
 *
 */
 
class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;
}
