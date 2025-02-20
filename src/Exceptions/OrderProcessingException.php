<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Exceptions;

use Exception;

class OrderProcessingException extends Exception
{
    public function __construct($message = 'Error processing order', $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
