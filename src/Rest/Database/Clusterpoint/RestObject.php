<?php
namespace Fathomminds\Clurexid\Rest\Database\Clusterpoint;

use Fathomminds\Clurexid\Rest\Objects\RestObject as CoreRestObject;

class RestObject extends CoreRestObject
{
    protected $primaryKey = '_id';
    protected $databaseClass = Database::class;
}
