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
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Ambil user yang sedang login
        $user = Auth::user();

        // 3. Cek apakah role user ada di dalam daftar role yang diizinkan
        // Logic ini support multiple roles, misal: middleware('role:admin,toolman')
        // Atau single role: middleware('role:siswa')
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Jika role tidak cocok (Unauthorized)
        // Kita redirect ke route '/dashboard' yang sudah ada logic redirector-nya
        // Jadi user akan dikembalikan ke dashboard role aslinya.
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki otoritas untuk mengakses halaman tersebut.');
    }
}