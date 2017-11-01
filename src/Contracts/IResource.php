<?php
namespace Fathomminds\Rest\Contracts;

interface IResource
{
    public function get($resourceId = null);
    public function post($newResource);
    public function patch($resourceId, $newResource);
    public function put($resourceId, $newResource);
    public function delete($resourceId);
}
