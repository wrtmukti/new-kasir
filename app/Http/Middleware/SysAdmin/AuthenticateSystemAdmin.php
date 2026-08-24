<?php

namespace App\Http\Middleware\SysAdmin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateSystemAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('system_admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi System Admin Anda telah berakhir. Silakan login kembali.',
                ], 401);
            }

            return redirect()->route('sys_admin.login')->with('warning', 'Silakan masuk terlebih dahulu untuk mengakses Control Panel.');
        }

        // Cek apakah akun aktif
        $user = Auth::guard('system_admin')->user();
        if ($user && (!$user->is_active || $user->delete_status == 1)) {
            Auth::guard('system_admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('sys_admin.login')->with('error', 'Akun System Admin Anda dinonaktifkan atau telah ditangguhkan.');
        }

        return $next($request);
    }
}
