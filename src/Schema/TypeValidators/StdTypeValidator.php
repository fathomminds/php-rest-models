<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Contracts\ITypeValidator;
use Fathomminds\Rest\Exceptions\RestException;

abstract class StdTypeValidator implements ITypeValidator
{
    protected $validType;
    private $updateMode = false;

    public function updateMode($updateMode = null)
    {
        if (is_bool($updateMode)) {
            $this->updateMode = $updateMode;
        }
        return $this->updateMode;
    }

    public function validate($value)
    {
        $this->validateType($value);
    }

    protected function validateType($value)
    {
        $currentType = gettype($value);
        if ($currentType !== $this->validType) {
            throw new RestException(
                'Type mismatch',
                [
                    'incorrectType' => $currentType,
                    'correctType' => $this->validType
                ]
            );
        }
    }

    public static function cast($value, $params = null)
    {
        return $value;
    }
}
