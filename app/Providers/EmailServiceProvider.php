<?php

namespace App\Providers;

use App\Services\EmailService;
use Illuminate\Support\ServiceProvider;

class EmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EmailService::class, function ($app) {
            return new EmailService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Регистрация консольных команд
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SyncExternalEmails::class,
                \App\Console\Commands\SendScheduledEmails::class,
                \App\Console\Commands\CleanEmailAttachments::class,
            ]);
        }
    }
}
