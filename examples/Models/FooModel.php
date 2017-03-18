<?php
namespace Fathomminds\Rest\Examples\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\Models\Objects\FooObject;

class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;
}
