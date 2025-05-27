<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Check if the user is authenticated in the admin guard
        if (!Auth::guard('admin')->check()) {
            // Store the intended URL for redirection after login
            if ($request->is('admin/*') || $request->is('admin')) {
                session()->put('url.intended', $request->url());
            }
            return redirect()->route('admin.login')->with('error', 'Please login to access the admin area');
        }

        // If role is specified, check if admin has that role
        if ($role && Auth::guard('admin')->user()->role !== $role) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access that section');
        }

        return $next($request);
    }
}
