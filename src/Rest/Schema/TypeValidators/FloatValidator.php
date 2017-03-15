<?php
namespace Fathomminds\Clurexid\Rest\Schema\TypeValidators;

use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;

class FloatValidator extends NumberTypeValidator
{
    protected $validType = 'float';
}
