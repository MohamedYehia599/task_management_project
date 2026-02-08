<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class CustomException extends Exception
{
    private int $statusCode;
    private string $errorMessage;
    private array $errors;
    
    public function __construct(
        string $message = '',
        int $statusCode = 400,
        array $errors = []
    ) {
        parent::__construct($message);
        
        $this->errorMessage = $message;
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }
    
   
    
    
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->errorMessage,
            'errors' => $this->errors,
        ], $this->statusCode);
    }
}