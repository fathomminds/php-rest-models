<?php
namespace Fathomminds\Rest\Schema\TypeValidators;

use Fathomminds\Rest\Exceptions\RestException;

class BooleanValidator extends StdTypeValidator
{
    protected $validType = 'boolean';
}
