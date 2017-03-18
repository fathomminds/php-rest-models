<?php
namespace Fathomminds\Rest\Helpers;

use ReflectionClass;

class ReflectionHelper
{
    public function createInstance($className, $params = [])
    {
        $class = new ReflectionClass($className);
        $instance = $class->newInstanceArgs($params);
        return $instance;
    }

    public function createMethod($className, $methodName)
    {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        return $method;
    }
}
