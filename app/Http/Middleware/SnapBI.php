<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AccountController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class SnapBI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = AccountController::CustomHeaders;
        if (!auth()->check()) {
            return response()->json([
                'status' => 401,
                'message' => "Unauthenticated"
            ], 401);
        }
        foreach ($authHeader as $key => $value) {
            if ($request->header($key) !== $value) {
                return response()->json([
                    'status' => 503,
                    'message' => "Please recheck your custom header"
                ], 503);
            }
        }

        return $next($request);
    }
}
