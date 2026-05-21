<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Только редирект для неавторизованных
        $middleware->redirectGuestsTo('/login');

        $middleware->alias([
            'require.company' => \App\Http\Middleware\RequireCompany::class,
            'haveCompanies' => \App\Http\Middleware\CheckUserCompanies::class,
            'checkUserRole' => \App\Http\Middleware\CheckUserRole::class,
            'isLeader' => \App\Http\Middleware\LeaderMiddleware::class,
            'isManager' => \App\Http\Middleware\ManagerMiddleware::class,
            'trackUserActivity' => \App\Http\Middleware\TrackUserActivity::class,
        ]);

        $middleware->web(append: [
            'trackUserActivity',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
