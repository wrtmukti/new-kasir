<?php

namespace App\Http\Middleware\SysAdmin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemAdminRole
{
    /**
     * Handle an incoming request for RBAC roles.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::guard('system_admin')->user();

        if (!$user) {
            return redirect()->route('sys_admin.login');
        }

        // Super admin has universal bypass
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses (permission) untuk melakukan tindakan ini.',
                ], 403);
            }

            abort(403, 'Akses Ditolak: Anda tidak memiliki izin role yang memadai.');
        }

        return $next($request);
    }
}
