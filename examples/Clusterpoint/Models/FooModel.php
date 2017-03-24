<?php
namespace Fathomminds\Rest\Examples\Clusterpoint\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\Clusterpoint\Models\Objects\FooObject;

class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;
}
