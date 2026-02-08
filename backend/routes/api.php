<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\JWTAuthMiddleware;
use App\Http\Middleware\EnsureTaskExists;
use App\Http\Controllers\Api\TaskController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes 
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refreshUserTokens']);
});


Route::middleware([JWTAuthMiddleware::class])->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
    
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);        
        Route::get('/{task}', [TaskController::class, 'show']);
        Route::patch('/{task}', [TaskController::class, 'update']);
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus']);
        Route::post('/{task}/dependencies', [TaskController::class, 'addDependencies']);
        
    });

});