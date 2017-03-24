<?php
namespace Fathomminds\Rest\Contracts;

interface ISchema
{
    public function setDefault($fieldName, $value);
    public function validate($resource);
    public function getFields();
    public function getRequiredFields();
    public function getUniqueFields();
}
