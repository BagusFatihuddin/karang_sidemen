<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__.'/../routes/web.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
    then: function () {
    Route::middleware('force.json')
        ->prefix('api/v1')
        ->group(base_path('routes/api_v1.php'));
    }
    )
    ->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'force.json' => \App\Http\Middleware\ForceJsonResponse::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

    $exceptions->shouldRenderJsonWhen(
        fn (Request $request) => $request->is('api/*'),
    );

    $exceptions->render(function (
        Throwable $exception,
        Request $request
    ) {
        if (! $request->is('api/*')) {
            return null;
        }

        return \App\Http\Responses\ApiResponse::error(
            $exception->getMessage(),
            method_exists($exception, 'getStatusCode')
                ? $exception->getStatusCode()
                : 500
        );
    });
    })
    ->create();
