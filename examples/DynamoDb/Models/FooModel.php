<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models;

use Fathomminds\Rest\RestModel;
use Fathomminds\Rest\Examples\DynamoDb\Models\Objects\FooObject;

class FooModel extends RestModel
{
    protected $restObjectClass = FooObject::class;
}
