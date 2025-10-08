<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;
use RealRashid\SweetAlert\Facades\Alert;
use \App\Http\Middleware\EnsureSubscription;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php', // Ajout des routes API
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        /** Sans alias */
        // $middleware->append([CheckRole::class]);

        /** Avec alias */
        $middleware->alias([
            'CheckRole' => CheckRole::class,
            'Alert' => Alert::class,
            'subscription' => EnsureSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
