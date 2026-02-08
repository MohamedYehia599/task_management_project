<?php

namespace App\Exceptions;

class ValidationException extends CustomException
{
    public function __construct(array $errors = [])
    {
        $message = 'Validation failed';
        parent::__construct($message, 400, $errors);
    }
}