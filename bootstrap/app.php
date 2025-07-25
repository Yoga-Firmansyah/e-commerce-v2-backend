<?php

use App\Http\Middleware\JWTMiddleware;
use App\Http\Middleware\VerifyJwt;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__ . '/../routes/web.php',
        // api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // health: '/up',
        using: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectTo(
            guests: '/',
            users: '/admin/dashboard'
        );
        // $middleware->append(VerifyJwt::class);
        // $middleware->append(JWTMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                try {
                    JWTAuth::parseToken()->authenticate();
                } catch (\Exception $e) {
                    if ($e)
                    // TODO proper error validation
                if ($e instanceof JWTException) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                    return response()->json(['status' => 'Invalid Token']);
                } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    return response()->json(['status' => 'Expired Token']);
                } else {
                    return response()->json(['status' => 'Token not found']);
                }
            }
        }
        });
    })->create();
