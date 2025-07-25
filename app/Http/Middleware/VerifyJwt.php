<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
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
        return $next($request);
    }
}
