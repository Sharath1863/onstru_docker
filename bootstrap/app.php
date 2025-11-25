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
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware (runs on every request)
        // $middleware->append(\App\Http\Middleware\YourGlobalMiddleware::class);

        // $middleware->append(\App\Http\Middleware\EncryptCookies::class);

        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
            'beta/payment/webhook',
        ]);

        // $middleware->append(\App\Http\Middleware\CorsMiddleware::class);

        // 2. ADD the correct configuration using the fluent method
        $middleware->encryptCookies(except: [
            'user_data', // <-- This is the only cookie that will NOT be encrypted
        ]);

        // Route middleware (used with ->middleware('alias') in routes)
        $middleware->alias([
            'checkuserauth' => \App\Http\Middleware\CheckUserAuth::class,
            'trustproxies' => \App\Http\Middleware\TrustProxies::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
