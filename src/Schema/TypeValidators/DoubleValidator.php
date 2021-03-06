<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

class DoubleValidator extends NumberTypeValidator
{
    protected $validType = 'double';

    public static function cast($value, $params = null, $skipExtraneous = false)
    {
        if (is_integer($value)) {
            return (double)$value;
        }
        return $value;
    }
}
