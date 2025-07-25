<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        
        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED && Auth::check()) {
            // Handle token refresh logic here
            $user = Auth::user();

            // Assume you're using Laravel Sanctum or Passport
            $newToken = $user->createToken('New Access Token')->plainTextToken;
            dd($newToken);

            // Add the new token to the response headers
            $response->headers->set('Authorization', 'Bearer ' . $newToken);

            // You can also update the request to prevent infinite loops
            $request->headers->set('Authorization', 'Bearer ' . $newToken);

            // Optionally return the new token in the response body as well
            $response->setContent(array_merge($response->original, ['token' => $newToken]));
        }
        return $response;

        $response = $next($request);
    }
}
