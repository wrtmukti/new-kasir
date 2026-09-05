<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Models\SysAdmin\Client;
use App\Services\Client\ClientDatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Tampilkan Halaman Form Login Admin POS
     */
    public function showLoginForm()
    {
        $clientDb = session('client_database') ?? session('tenant_database');

        if ($clientDb) {
            $connected = ClientDatabaseManager::connectToClient($clientDb);
            if ($connected && Auth::guard('web')->check()) {
                $authUser = Auth::guard('web')->user();
                if ($authUser && $authUser->role === 'admin') {
                    return redirect()->route('owner.dashboard');
                }
                return redirect()->route('admin.order.index');
            }

            if (!$connected) {
                session()->forget([
                    'client_database',
                    'client_id',
                    'client_name',
                    'client_code',
                    'business_name',
                    'tenant_database',
                    'tenant_client_id',
                    'tenant_client_name',
                    'tenant_business_name',
                    'is_impersonating',
                    'impersonated_client_id',
                    'impersonated_client_name',
                ]);
                Auth::guard('web')->logout();
                ClientDatabaseManager::connectToCentral();
            }
        }

        return view('admin.auth.login');
    }

    /**
     * Proses Login Admin POS & Kasir Multi-Client
     */
    public function login(LoginRequest $request)
    {
        $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $msg = "Terlalu banyak percobaan login gagal. Silakan coba kembali dalam {$seconds} detik.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }

            return back()->withErrors(['email' => $msg])->withInput();
        }

        $email = $request->input('email');
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // 1. Cari database client berdasarkan email di Central DB
        ClientDatabaseManager::connectToCentral();
        $client = Client::where('owner_email', $email)
            ->where('status', 'active')
            ->where('delete_status', 0)
            ->first();

        // Jika bukan owner langsung, cari di seluruh client aktif
        if (!$client) {
            $activeClients = Client::where('status', 'active')->where('delete_status', 0)->get();
            foreach ($activeClients as $c) {
                if (ClientDatabaseManager::connectToClient($c->database_name)) {
                    $hasUser = DB::connection('client')
                        ->table('users')
                        ->where('email', $email)
                        ->exists();
                    if ($hasUser) {
                        $client = $c;
                        break;
                    }
                }
            }
        }

        // 2. Hubungkan ke database client yang sesuai
        if ($client) {
            ClientDatabaseManager::connectToClient($client->database_name);
            session(['client_database' => $client->database_name]);
        }

        // 3. Attempt otentikasi guard web pada DB client
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            if ($client) {
                session([
                    'client_database' => $client->database_name,
                    'client_id' => $client->client_id,
                    'client_name' => $client->client_name,
                    'client_code' => $client->client_code,
                    'business_name' => $client->business_name,
                    // Backward compatibility keys
                    'tenant_database' => $client->database_name,
                    'tenant_client_id' => $client->client_id,
                    'tenant_client_name' => $client->client_name,
                    'tenant_business_name' => $client->business_name,
                ]);
            }

            $user = Auth::guard('web')->user();

            // Tentukan target redirect & inisialisasi outlet aktif
            if ($user->role === 'admin') {
                $targetUrl = route('owner.dashboard');
            } else {
                // Untuk kasir: kunci sesi ke cabang outlet yang ditugaskan
                if ($user->outlet_id) {
                    session([
                        'active_outlet_id' => $user->outlet_id,
                        'outlet_id' => $user->outlet_id,
                    ]);
                    $outlet = DB::connection('client')->table('outlets')->where('outlet_id', $user->outlet_id)->first();
                    if ($outlet) {
                        session([
                            'active_outlet_name' => $outlet->outlet_name,
                            'current_outlet_name' => $outlet->outlet_name,
                        ]);
                    }
                }
                $targetUrl = route('admin.order.index');
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Selamat datang kembali, {$user->name}!",
                    'redirect_url' => $targetUrl,
                ]);
            }

            return redirect()->intended($targetUrl)
                ->with('success', "Selamat datang kembali, {$user->name}!");
        }

        RateLimiter::hit($throttleKey, 60);

        $errorMsg = 'Email atau kata sandi yang Anda masukkan tidak sesuai.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $errorMsg], 422);
        }

        return back()->withErrors(['email' => $errorMsg])->withInput();
    }

    /**
     * Proses Logout Admin POS & Kasir
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->forget([
            'client_database',
            'client_id',
            'client_name',
            'client_code',
            'business_name',
            'tenant_database',
            'tenant_client_id',
            'tenant_client_name',
            'tenant_business_name',
            'is_impersonating',
            'impersonated_client_id',
            'impersonated_client_name',
        ]);

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        ClientDatabaseManager::connectToCentral();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari akun kasir.');
    }
}
