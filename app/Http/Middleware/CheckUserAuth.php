<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAuth
{
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            // Log::info('CheckUserAuth middleware', ['user_id' => $request->user()->id ?? null]);

            // Update updated_at for the authenticated user
            if ($request->user()) {
                $request->user()->touch(); // Laravel touch() updates updated_at
            }

            return $next($request);
        }

        $userId = $request->cookie('user_id');
        if ($userId) {
            Auth::loginUsingId($userId);

            return $next($request);
        }

        // If this is an AJAX request (Echo / broadcasting), return JSON 403
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 403);
        }

        // Save the current URL before redirect
        session(['url.intended' => $request->fullUrl()]);

        return redirect()->route('login');
    }
}
