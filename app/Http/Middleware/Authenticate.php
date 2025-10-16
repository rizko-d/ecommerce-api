<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated user
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Untuk web routes, redirect ke login
        return route('login');
    }

    /**
     * Handle incoming request
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        if ($request->is('api/*') && in_array('api', $guards)) {
            $token = $request->bearerToken();
            
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Token required.'
                ], 401);
            }

            $user = \App\Models\User::where('api_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token.'
                ], 401);
            }
            // Set authenticated user
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            return $next($request);
        }
        // Default authentication untuk web routes
        return parent::handle($request, $next, ...$guards);
    }
}