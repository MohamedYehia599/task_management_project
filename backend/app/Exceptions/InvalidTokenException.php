<?php

namespace App\Exceptions;

use Exception;

class InvalidTokenException extends CustomException
{
    public function __construct()
    {
        $message = 'Invalid Token';
        parent::__construct($message, 401, []);
    }
}
