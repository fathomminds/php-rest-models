<?php
namespace Fathomminds\Clurexid\Rest\Schema\TypeValidators;

use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;

class DoubleValidator extends NumberTypeValidator
{
    protected $validType = 'double';
}
