<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Contracts\ITypeValidator;
use Fathomminds\Rest\Exceptions\DetailedException;

abstract class StdTypeValidator implements ITypeValidator
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
