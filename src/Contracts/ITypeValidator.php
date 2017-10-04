<?php
namespace Fathomminds\Rest\Contracts;

interface ITypeValidator
{
    public function validate($value);
    public function updateMode($updateMode = null);
}
