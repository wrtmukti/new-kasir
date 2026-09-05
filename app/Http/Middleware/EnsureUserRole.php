<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        $userRole = strtolower($user->role ?? 'kasir');

        // Jika ada spesifikasi role, cek apakah role user ada dalam daftar yang diizinkan
        if (!empty($roles) && !in_array($userRole, array_map('strtolower', $roles))) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Anda tidak memiliki wewenang untuk membuka menu ini.'], 403);
            }

            // Jika kasir mencoba akses menu khusus owner, kembalikan ke kasir POS
            if ($userRole === 'kasir') {
                return redirect()->route('admin.order.index')
                    ->with('error', 'Akses ditolak: Menu ini khusus untuk Owner.');
            }

            return redirect()->route('owner.dashboard')
                ->with('error', 'Akses ditolak: Anda tidak memiliki hak akses ke menu tersebut.');
        }

        return $next($request);
    }
}
