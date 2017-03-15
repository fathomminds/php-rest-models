<?php
namespace Fathomminds\Clurexid\Rest\Schema\TypeValidators;

use Fathomminds\Clurexid\Rest\Contracts\ITypeValidator;
use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;

class AnyValidator implements ITypeValidator
{
    public function validate($value)
    {
        return $value;
    }
}
