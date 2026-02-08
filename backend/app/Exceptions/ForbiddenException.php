<?php

namespace App\Exceptions;

class ForbiddenException extends CustomException
{
    public function __construct()
    {
        $message = 'Access denied';
        parent::__construct($message, 403, []);
    }
}