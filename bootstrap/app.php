<?php

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('api', \App\Http\Middleware\ForceJsonResponse::class);
        $middleware->alias([
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $response = new class {
            use ApiResponse;
        };

        $exceptions->renderable(function (ValidationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->error('Validation failed', 422, $e->errors());
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->error('Unauthenticated', 401);
            }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->error($e->getMessage() ?: 'HTTP error', $e->getStatusCode());
            }
        });

        $exceptions->renderable(function (\Throwable $e, Request $request) use ($response) {
            if ($request->is('api/*')) {
                return $response->error(
                    config('app.debug') ? $e->getMessage() : 'Internal Server Error',
                    500
                );
            }
        });
    })
    ->create();