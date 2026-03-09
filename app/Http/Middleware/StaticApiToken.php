<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StaticApiToken
{
    public function handle(Request $request, Closure $next)
    {
        // Define your hardcoded token here
        $validToken = 'vK9mX2pL8nQ4wZ1rT7bY3vJ6hG9sD5fA2kR0'; 

        // Check the 'X-API-KEY' header
        if ($request->header('X-API-KEY') !== $validToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid API Key.'
            ], 401);
        }

        return $next($request);
    }
}