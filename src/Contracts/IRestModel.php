<?php
namespace Fathomminds\Rest\Contracts;

interface IRestModel
{
    public function use($obj);
    public function resource();
    public function one($resourceId);
    public function all();
    public function create();
    public function update();
    public function delete();
    public function validate();
    public function toArray();
}
