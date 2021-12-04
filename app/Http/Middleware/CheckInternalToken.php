<?php

namespace App\Http\Middleware;

use Closure;

class CheckInternalToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $api_token = env('API_HOST_TOKEN') ?? '';
        $token = $request->bearerToken();
        if (!isset($token) || $token != $api_token) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
