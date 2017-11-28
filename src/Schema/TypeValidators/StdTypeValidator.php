<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Contracts\ITypeValidator;
use Fathomminds\Rest\Exceptions\RestException;

abstract class StdTypeValidator implements ITypeValidator
{
    protected $validType;
    private $updateMode = false;
    private $replaceMode = false;

    protected $nullable = false;

    public function __construct($params = [])
    {
        if (isset($params['nullable']) && is_bool($params['nullable'])) {
            $this->nullable = $params['nullable'];
        }
    }

    public static function cast($value, $params = null, $skipExtraneous = false)
    {
        return $value;
    }

    public function updateMode($updateMode = null)
    {
        if (is_bool($updateMode)) {
            $this->updateMode = $updateMode;
        }
        return $this->updateMode;
    }

    public function replaceMode($replaceMode = null)
    {
        if (is_bool($replaceMode)) {
            $this->replaceMode = $replaceMode;
        }
        return $this->replaceMode;
    }

    public function validate($value)
    {
        $this->validateType($value);
    }

    protected function validateType($value)
    {
        $currentType = gettype($value);
        if ($currentType !== $this->validType && ($value !== null || !$this->nullable)) {
            throw new RestException(
                'Type mismatch',
                [
                    'incorrectType' => $currentType,
                    'correctType' => $this->validType
                ]
            );
        }
    }
}
