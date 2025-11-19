<?php

namespace App\Http\Middleware;

use Closure;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if(!$user){ 
            redirect()->route('landing');
        }

        if($user->role === 'admin'){
            return $next($request); 
        }

        return response()->view('errors.403', [], 403);
    }
}
