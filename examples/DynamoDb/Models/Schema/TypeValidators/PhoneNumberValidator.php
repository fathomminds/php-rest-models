<?php
namespace Fathomminds\Rest\Examples\DynamoDb\Models\Schema\TypeValidators;

use Fathomminds\Rest\Schema\TypeValidators\StdTypeValidator;
use Fathomminds\Rest\Exceptions\RestException;

class PhoneNumberValidator extends StdTypeValidator
{
    protected $validType = 'string';

    public function validate($value)
    {
        $this->validateType($value);
        throw new RestException(
            'Something is wrong...'
        );
    }
}
