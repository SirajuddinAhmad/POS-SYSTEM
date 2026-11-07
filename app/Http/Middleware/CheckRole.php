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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Akses ditolak. Anda tidak memiliki izin untuk tindakan ini.'], 403);
            }

            if ($user->role === 'cashier') {
                return redirect()->route('pos.index')->with('error', 'Akses ditolak: Halaman ini hanya untuk Administrator.');
            }

            return redirect()->route('admin.dashboard')->with('error', 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
