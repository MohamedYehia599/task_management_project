<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException as LaravelValidationException;
use Illuminate\Auth\AuthenticationException as LaravelAuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        return $this->handleApiException($exception);   
    }
    
    /**
     * Handle API exceptions 
     */
    private function handleApiException(Throwable $exception): JsonResponse
    {
        // If it's  custom exception
        if ($exception instanceof CustomException) {
            return $exception->render();
        }

        if ($exception instanceof LaravelValidationException) {
            return (new ValidationException(
                $exception->errors(),              
            ))->render();
        }

        if ($exception instanceof ModelNotFoundException) {
            return (new NotFoundException())->render();           
        }

        

        
        // if ($exception instanceof LaravelAuthenticationException) {
        //     return (new InvalidCredentialsException())->render();
            
        // }

        if ($exception instanceof AuthorizationException) {
            return (new ForbiddenException($exception->getMessage()))->render();
        }

        
        return (new SomethingWentWrongException())->render();
    }


}
