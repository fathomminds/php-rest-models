<?php
namespace Fathomminds\Clurexid\Rest\Examples\Models;

use Fathomminds\Clurexid\Rest\RestModel;
use Fathomminds\Clurexid\Rest\Examples\Models\Objects\FooObject;

class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;
}
