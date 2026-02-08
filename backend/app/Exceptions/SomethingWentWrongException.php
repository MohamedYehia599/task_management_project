<?php

namespace App\Exceptions;

class SomethingWentWrongException extends CustomException
{
    public function __construct()
    {
        $message = 'Something went wrong';
        parent::__construct($message, 500, []);
    }
}