<?php
namespace Fathomminds\Rest\Database;

class FinderQueryConfiguration
{
    public $databaseName;
    public $select = '*';
    public $from;
    public $where;
    public $orderBy = [];
    public $limit;
    public $offset;
}
