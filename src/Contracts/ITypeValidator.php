<?php
namespace Fathomminds\Rest\Contracts;

interface ITypeValidator
{
    public static function cast($value, $params = null);
    public function updateMode($updateMode = null);
    public function replaceMode($replaceMode = null);
    public function validate($value);
}
