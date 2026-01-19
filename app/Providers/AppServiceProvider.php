<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\Email;
use App\Observers\TaskObserver;
use App\Observers\EmailObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрация сервисов
        $this->app->singleton(\App\Services\EmailService::class, function ($app) {
            return new \App\Services\EmailService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Task::observe(TaskObserver::class);
        Email::observe(EmailObserver::class);
    }
}
