<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\RestException;

class EnumValidator extends StdTypeValidator
{
    protected $validValues = [];

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->validValues = isset($params['validValues']) ? $params['validValues'] : [];
    }

    public function validate($value)
    {
        $this->validateValue($value);
    }

    private function validateValue($value)
    {
        if (!(in_array($value, $this->validValues, true) || ($value === null && $this->nullable))) {
            throw new RestException(
                'Value is not in valid value list',
                [
                    'value' => $value,
                    'validValues' => $this->validValues,
                ]
            );
        }
    }
}
