<?php
namespace Fathomminds\Clurexid\Rest\Schema\TypeValidators;

use Fathomminds\Clurexid\Rest\Contracts\ITypeValidator;
use Fathomminds\Clurexid\Rest\Exceptions\DetailedException;

class StdTypeValidator implements ITypeValidator
{
    protected $validType;

    public function validate($value)
    {
        $this->validateType($value);
    }

    protected function validateType($value)
    {
        $currentType = gettype($value);
        if ($currentType !== $this->validType) {
            throw new DetailedException(
                'Type mismatch',
                [
                    'incorrectType' => $currentType,
                    'correctType' => $this->validType
                ]
            );
        }
    }
}
