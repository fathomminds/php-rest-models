<?php
namespace Fathomminds\Clurexid\Rest\Exceptions;

use \Exception;

class DetailedException extends Exception
{
    private $details;

    public function __construct(
        $message,
        $details = [],
        $code = 0,
        Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }

    public function getDetails()
    {
        return $this->details;
    }
}
