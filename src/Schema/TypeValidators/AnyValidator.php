<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Contracts\ITypeValidator;
use Fathomminds\Rest\Exceptions\DetailedException;

class AnyValidator implements ITypeValidator
{
    public function validate($value)
    {
        return $value;
    }
}
