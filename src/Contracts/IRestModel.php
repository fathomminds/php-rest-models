<?php
namespace Fathomminds\Rest\Contracts;

interface IRestModel
{
    public function resource($resource = null);
    public function one($resourceId);
    public function all();
    public function create();
    public function update();
    public function delete();
    public function validate();
    public function toArray();
}
