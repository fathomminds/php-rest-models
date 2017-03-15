<?php
namespace Fathomminds\Clurexid\Rest\Schema\TypeValidators;

use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;

class IntegerValidator extends NumberTypeValidator
{
    protected $validType = 'integer';
}
