<?php
namespace Fathomminds\Clurexid\Rest\Contracts;

interface ISchema
{
    public function validate($resource);
    public function getRequiredFields();
    public function getUniqueFields();
}
