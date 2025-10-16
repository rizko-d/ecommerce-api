<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccessKey
{

    public function handle(Request $request, Closure $next)
    {
        $accessKey = $request->header('X-Access-Key');
        
        // Validasi access key
        if (!$accessKey || $accessKey !== config('app.access_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing access key.'
            ], 401);
        }

        return $next($request);
    }
}
