<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\DetailedException;

class DoubleValidator extends NumberTypeValidator
{
    protected $validType = 'double';
}
