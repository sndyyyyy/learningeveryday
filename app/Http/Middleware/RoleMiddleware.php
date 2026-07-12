<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login dan role-nya ada di dalam daftar parameter $roles
        if (Auth::check() && in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // Jika peserta mencoba masuk area admin, lempar ke dashboard peserta
        if (Auth::check() && Auth::user()->role === 'peserta') {
            return redirect('/peserta/dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut.');
        }

        // Default: tendang ke halaman login
        return redirect('/login')->with('error', 'Akses ditolak.');
    }
}