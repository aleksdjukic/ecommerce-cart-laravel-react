<?php

use App\Exceptions\InsufficientStockException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpFoundation\Response;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | Web Middleware (Inertia)
        |--------------------------------------------------------------------------
        */
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | API Middleware (Sanctum SPA)
        |--------------------------------------------------------------------------
        | React/Inertia SPA
        */
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {

        /*
        |--------------------------------------------------------------------------
        | 422 — Validation errors
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (
            ValidationException $e,
            $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | 403 — Authorization
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (
            AuthorizationException $e,
            $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You are not allowed to perform this action',
                ], Response::HTTP_FORBIDDEN);
            }
        });

        /*
        |--------------------------------------------------------------------------
        | 422 — Business rule (stock)
        |--------------------------------------------------------------------------
        */
        $exceptions->render(function (
            InsufficientStockException $e,
            $request
        ) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        });
    })

    ->create();
