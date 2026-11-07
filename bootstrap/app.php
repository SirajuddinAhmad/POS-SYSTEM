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
    ->withMiddleware(function (Middleware $middleware) {
        // Vercel (dan proxy lain) mengirim skema asli lewat X-Forwarded-*.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
            'pos/midtrans/notification',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
