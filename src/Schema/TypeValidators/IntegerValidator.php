<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\DetailedException;

class IntegerValidator extends NumberTypeValidator
{
    protected $validType = 'integer';
}
