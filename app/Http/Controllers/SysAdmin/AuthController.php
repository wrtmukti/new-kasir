<?php

namespace App\Http\Controllers\SysAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SysAdmin\Auth\LoginRequest;
use App\Models\SysAdmin\SystemUser;
use App\Models\SysAdmin\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman Login System Admin
     */
    public function showLoginForm()
    {
        if (Auth::guard('system_admin')->check()) {
            return redirect()->route('sys_admin.dashboard');
        }

        return view('sys_admin.auth.login');
    }

    /**
     * Proses autentikasi System Admin
     */
    public function login(LoginRequest $request)
    {
        $loginField = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Rate limiting key
        $throttleKey = Str::transliterate(Str::lower($loginField) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $errMsg = "Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam {$seconds} detik.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errMsg,
                ], 429);
            }

            return back()->withErrors(['login' => $errMsg])->withInput();
        }

        // Tentukan apakah input berupa email atau username
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginField,
            'password' => $password,
            'delete_status' => 0,
        ];

        if (Auth::guard('system_admin')->attempt($credentials, $remember)) {
            $user = Auth::guard('system_admin')->user();

            // Cek apakah akun aktif
            if (!$user->is_active) {
                Auth::guard('system_admin')->logout();

                $errMsg = 'Akun Anda dinonaktifkan oleh Administrator. Hubungi Super Admin.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $errMsg], 403);
                }
                return back()->withErrors(['login' => $errMsg])->withInput();
            }

            // Bersihkan rate limiter
            RateLimiter::clear($throttleKey);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            // Update info login
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);

            // Catat Audit Log
            AuditLog::record(
                action: 'sysadmin_login',
                targetType: 'SystemUser',
                targetId: (string) $user->id,
                result: 'success',
                metadata: [
                    'username' => $user->username,
                    'role' => $user->role,
                    'ip' => $request->ip(),
                ]
            );

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Selamat datang kembali, {$user->name}!",
                    'redirect_url' => route('sys_admin.dashboard'),
                ]);
            }

            return redirect()->intended(route('sys_admin.dashboard'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        // Jika gagal
        RateLimiter::hit($throttleKey, 60);

        AuditLog::record(
            action: 'sysadmin_login_failed',
            targetType: 'SystemUser',
            targetId: null,
            result: 'failure',
            metadata: [
                'attempted_login' => $loginField,
                'ip' => $request->ip(),
            ]
        );

        $errMsg = 'Email/Username atau kata sandi yang Anda masukkan salah.';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errMsg,
            ], 422);
        }

        return back()->withErrors(['login' => $errMsg])->withInput();
    }

    /**
     * Logout System Admin
     */
    public function logout(Request $request)
    {
        $user = Auth::guard('system_admin')->user();

        if ($user) {
            AuditLog::record(
                action: 'sysadmin_logout',
                targetType: 'SystemUser',
                targetId: (string) $user->id,
                result: 'success',
                metadata: ['username' => $user->username]
            );
        }

        Auth::guard('system_admin')->logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('sys_admin.login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
