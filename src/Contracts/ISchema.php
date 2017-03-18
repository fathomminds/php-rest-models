<?php
namespace Fathomminds\Rest\Contracts;

interface ISchema
{
    public function validate($resource);
    public function getRequiredFields();
    public function getUniqueFields();
}
