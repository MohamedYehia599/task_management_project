<?php

namespace App\Exceptions;

class InvalidCredentialsException extends CustomException
{
    public function __construct()
    {
        $message = 'Invalid credentials';
        parent::__construct($message, 401, []);
    }
}