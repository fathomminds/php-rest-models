<?php
namespace Fathomminds\Rest\Examples\Models\Schema\TypeValidators;

use Fathomminds\Rest\Schema\TypeValidators\StdTypeValidator;
use Fathomminds\Rest\Exceptions\DetailedException;

class PhoneNumberValidator extends StdTypeValidator
{
    protected $validType = 'string';

    public function validate($value)
    {
        $this->validateType($value);
        throw new DetailedException(
            'Something is wrong...'
        );
    }
}
