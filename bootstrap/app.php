<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {

            if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 403])) {
                return back()->with([
                    'error' => $response->getStatusCode() . ': ' . $exception->getMessage(),
                ]);
            } elseif ($response->getStatusCode() === 419) {

                return response()->json([
                    'status' => false,
                    'message' => 'The page expired, please try again.',
                    'errors' => [],
                ], 419);
            }elseif ($response->getStatusCode() === 404) {

                return response()->json([
                    'status' => false,
                    'message' => $exception->getMessage(),
                    'errors' => [],
                ], 404);
            }

            return $response;
        });
    })->create();
