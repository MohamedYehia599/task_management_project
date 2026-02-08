<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\TaskRepositoryContract;
use App\Repositories\DB\TaskRepository;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\DB\UserRepository;
use App\Repositories\Contracts\AuthRepositoryContract;
use App\Repositories\Redis\RedisAuthRepo;
use App\Services\RedisClient;
use App\Services\RedisCircuitBreaker;  

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(TaskRepositoryContract::class, TaskRepository::class);
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        
        
        $this->app->singleton(RedisCircuitBreaker::class, function () {
            return new RedisCircuitBreaker();
        });
        
        
        $this->app->singleton(RedisClient::class, function ($app) {
            return new RedisClient(
                $app->make(RedisCircuitBreaker::class) 
            );
        });
        
        
        $this->app->bind(AuthRepositoryContract::class, function ($app) {
            return new RedisAuthRepo(
                $app->make(RedisClient::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}