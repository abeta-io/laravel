<?php

namespace AbetaIO\Laravel\Exceptions;

use Exception;

class ReturnCartException extends Exception
{
    public function __construct($message = "Failed to return cart", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
