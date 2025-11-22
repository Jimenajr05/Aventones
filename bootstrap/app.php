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
        // Middleware para bloquear usuarios Pendiente o Inactivo (Aventones)
        $middleware->web([
            \App\Http\Middleware\CheckUserStatus::class,
        ]);

        // Middleware de roles (Aventones)
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
