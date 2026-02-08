<?php

namespace App\Exceptions;

class UnauthenticatedException extends CustomException
{
    public function __construct($message = 'Unauthenticated')
    {
        
        parent::__construct($message, 401,[]);
    }
}
