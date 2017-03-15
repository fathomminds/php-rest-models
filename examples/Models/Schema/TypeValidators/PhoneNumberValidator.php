<?php
namespace Fathomminds\Clurexid\Rest\Examples\Models\Schema\TypeValidators;

use Fathomminds\Clurexid\Rest\Schema\TypeValidators\StdTypeValidator;
use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;

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
