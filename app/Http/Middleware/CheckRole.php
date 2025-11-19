<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('landing');
        }

        // ingin masuk ke admin, dan role nya admin
        if($role == 'admin' && $user->role == 'admin') {
            return $next($request);
        }

        // ingin masuk ke teacher, dan dia adalah seorang guru
        if ($role == 'teacher' && $user->is_teacher) {
            return $next($request);
        }

        // jika tidak memenuhi syarat, error
        return response()->view('errors.403', [], 403);
    }
}
