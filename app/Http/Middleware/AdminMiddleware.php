<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->user();
        if(!$admin) {
            return apiError('Unauthenticated',401);
        }
        if($admin->role !== 'admin') {
            return apiError('The user has no permission',403);
        }
        return $next($request);
    }
}
