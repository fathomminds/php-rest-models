<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Contracts\ITypeValidator;

class AnyValidator implements ITypeValidator
{
    public function validate($value)
    {
        return $value;
    }
}
