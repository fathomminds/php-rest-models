<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Contracts\ITypeValidator;

class AnyValidator extends StdTypeValidator
{
    public function validate($value)
    {
        return $value;
    }
}
