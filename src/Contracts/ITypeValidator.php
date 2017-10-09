<?php
namespace Fathomminds\Rest\Contracts;

interface ITypeValidator
{
    public static function cast($value, $params = null);
    public function validate($value);
    public function updateMode($updateMode = null);
}
