<?php
namespace Fathomminds\Rest\Contracts;

interface ISchema
{
    public static function cast($object);
    public function schema();
    public function toArray();
    public function setFieldDefaults();
    public function validate();
}
