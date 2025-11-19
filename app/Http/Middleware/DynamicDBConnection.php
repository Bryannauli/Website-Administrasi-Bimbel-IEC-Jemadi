<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DynamicDBConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = Auth::user()->role;

            switch ($role) {
                case 'admin':
                    Config::set('database.default', 'iec_mysql');
                    break;
                case 'teacher':
                    Config::set('database.default', 'iec_mysql');
                    break;
                default:
                    Config::set('database.default', 'mysql');
                    break;
            }

            // Reconnect untuk memastikan koneksi berubah
            DB::reconnect();
        }

        return $next($request);
    }
}
