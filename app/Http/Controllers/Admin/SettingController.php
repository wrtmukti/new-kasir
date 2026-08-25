<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\SettingOutlet;
use App\Models\Admin\Tax;
use App\Models\Admin\ServiceCharge;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Shift;
use App\Models\Admin\Outlet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Resolusi outlet aktif berdasarkan session atau outlet default.
     */
    private function resolveActiveOutlet(): Outlet
    {
        $activeOutletId = session('active_outlet_id') ?? session('outlet_id');
        $outlet = null;
        if ($activeOutletId) {
            $outlet = Outlet::where('outlet_id', $activeOutletId)->where('delete_status', 0)->first();
        }
        if (!$outlet) {
            $outlet = Outlet::where('delete_status', 0)->first();
        }
        if (!$outlet) {
            $outlet = Outlet::create([
                'outlet_id' => (string) \Illuminate\Support\Str::ulid(),
                'outlet_name' => 'Restoran & Cafe',
                'outlet_code' => 'RESTO',
                'outlet_branch' => 'Pusat',
                'created_by' => 'admin',
            ]);
        }
        return $outlet;
    }

    /**
     * Tampilkan halaman utama Setting Outlet & Kasir.
     */
    public function index()
    {
        $outlet = $this->resolveActiveOutlet();

        $setting = SettingOutlet::where('outlet_id', $outlet->outlet_id)
            ->where('delete_status', 0)
            ->first();

        if (!$setting) {
            $setting = SettingOutlet::create([
                'outlet_id' => $outlet->outlet_id,
                'outlet_name' => $outlet->outlet_name,
                'payment_timing' => 'post_payment',
                'theme' => config('app.guest_template', 'spicy_bites'),
                'created_by' => 'admin',
            ]);
        }

        // Data Master Pajak & Service Charge untuk Outlet Aktif
        $tax = Tax::where('outlet_id', $outlet->outlet_id)->where('is_active', 1)->first() ?? Tax::where('outlet_id', $outlet->outlet_id)->first() ?? Tax::first();
        $service = ServiceCharge::where('outlet_id', $outlet->outlet_id)->where('is_active', 1)->first() ?? ServiceCharge::where('outlet_id', $outlet->outlet_id)->first() ?? ServiceCharge::first();

        // Data Master Shift & Cut-Off
        $outletId = $outlet->outlet_id;
        $shiftSetting = ShiftSetting::where('outlet_id', $outletId)->first() 
            ?? ShiftSetting::first() 
            ?? new ShiftSetting([
                'daily_cutoff_time' => '03:00:00',
                'shift_mode' => 'auto_master',
                'auto_lock_unclosed' => 1,
            ]);

        $shifts = Shift::orderBy('shift_number', 'asc')->get();

        // Daftar 7 Tema Guest QR Ordering beserta metadata UI
        $themes = [
            [
                'key' => 'spicy_bites',
                'name' => 'Spicy Bites (Fire Red)',
                'badge' => 'Fire & Grill',
                'color' => '#dc2626',
                'accent' => '#b91c1c',
                'desc' => 'Tema berani dengan aksen api merah pedas, cocok untuk restoran ayam geprek, kuliner pedas, grill, dan seafood.',
                'icon' => 'bi-fire',
            ],
            [
                'key' => 'metropolis_brew',
                'name' => 'Metropolis Brew (Modern Blue)',
                'badge' => 'Urban & Cafe',
                'color' => '#2563eb',
                'accent' => '#1d4ed8',
                'desc' => 'Tampilan elegan, modern, dan minimalis dengan nuansa biru safir, sangat pas untuk cafe modern dan coffee shop.',
                'icon' => 'bi-cup-hot-fill',
            ],
            [
                'key' => 'ignite_spice',
                'name' => 'Ignite & Spice (Orange Flare)',
                'badge' => 'Dynamic Kitchen',
                'color' => '#ea580c',
                'accent' => '#c2410c',
                'desc' => 'Desain hangat penuh semangat dengan nuansa jingga hangat, cocok untuk cafe bistro, burger bar, dan resto casual.',
                'icon' => 'bi-lightning-charge-fill',
            ],
            [
                'key' => 'midnight_social',
                'name' => 'Midnight Social (Deep Violet)',
                'badge' => 'Lounge & Bar',
                'color' => '#7c3aed',
                'accent' => '#6d28d9',
                'desc' => 'Sleek dark mode beraksen ungu neon premium, sangat cocok untuk lounge malam, bar, resto rooftop, dan resto eksklusif.',
                'icon' => 'bi-moon-stars-fill',
            ],
            [
                'key' => 'omah_kopi_jogja',
                'name' => 'Omah Kopi Jogja (Warm Wood)',
                'badge' => 'Authentic Coffee',
                'color' => '#92400e',
                'accent' => '#78350f',
                'desc' => 'Nuansa kayu klasik dan otentik Nusantara, pas untuk kedai kopi tradisional, roastery, dan angkringan modern.',
                'icon' => 'bi-cup-straw',
            ],
            [
                'key' => 'bumblebee',
                'name' => 'Bumblebee (Golden Honey)',
                'badge' => 'Fresh & Bakery',
                'color' => '#d97706',
                'accent' => '#b45309',
                'desc' => 'Sentuhan kuning madu yang ceria dan ramah, ideal untuk toko kue, bakery, es krim, dessert shop, dan brunch spot.',
                'icon' => 'bi-sun-fill',
            ],
            [
                'key' => 'standard',
                'name' => 'Standard Nexora (Clean Neutral)',
                'badge' => 'Clean & Fast',
                'color' => '#475569',
                'accent' => '#334155',
                'desc' => 'Tampilan standar universal yang ringan, bersih, dan cepat untuk segala jenis outlet makanan & minuman.',
                'icon' => 'bi-grid-1x2-fill',
            ],
        ];

        return view('admin.setting.index', compact('outlet', 'setting', 'themes', 'tax', 'service', 'shiftSetting', 'shifts'));
    }

    /**
     * Update Pengaturan Alur Pembayaran (Bayar di Awal vs Bayar di Akhir).
     */
    public function updatePaymentSetting(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_timing' => 'required|in:post_payment,pre_payment',
        ]);

        $outlet = $this->resolveActiveOutlet();

        $setting = SettingOutlet::firstOrCreate(
            ['outlet_id' => $outlet->outlet_id],
            [
                'outlet_name' => $outlet->outlet_name,
                'created_by' => 'admin',
            ]
        );

        $setting->update([
            'payment_timing' => $validated['payment_timing'],
            'updated_by' => 'admin',
        ]);

        $label = ($validated['payment_timing'] === 'pre_payment') ? 'Bayar di Awal (Pre-Payment)' : 'Bayar di Akhir (Post-Payment)';

        return response()->json([
            'success' => true,
            'message' => 'Alur pembayaran berhasil diubah menjadi: ' . $label . '.',
            'payment_timing' => $setting->payment_timing,
        ]);
    }

    /**
     * Update Pengaturan Tema Guest Ordering.
     */
    public function updateThemeSetting(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'theme' => 'required|string|in:standard,spicy_bites,metropolis_brew,ignite_spice,midnight_social,omah_kopi_jogja,bumblebee',
        ]);

        $outlet = $this->resolveActiveOutlet();

        $setting = SettingOutlet::firstOrCreate(
            ['outlet_id' => $outlet->outlet_id],
            [
                'outlet_name' => $outlet->outlet_name,
                'created_by' => 'admin',
            ]
        );

        $setting->update([
            'theme' => $validated['theme'],
            'updated_by' => 'admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tema QR Guest Ordering berhasil diubah menjadi: ' . $validated['theme'] . '.',
            'theme' => $setting->theme,
        ]);
    }

    /**
     * Update Profil Outlet & Informasi Perusahaan.
     */
    public function updateCompanyProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'outlet_name' => 'required|string|max:150',
            'outlet_code' => 'nullable|string|max:20',
            'outlet_branch' => 'nullable|string|max:100',
            'outlet_email' => 'nullable|email|max:100',
            'outlet_phone' => 'nullable|string|max:30',
            'outlet_address' => 'nullable|string|max:500',
            'outlet_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $outlet = $this->resolveActiveOutlet();

        $updateData = [
            'outlet_name' => $validated['outlet_name'],
            'outlet_code' => $validated['outlet_code'] ?? $outlet->outlet_code,
            'outlet_branch' => $validated['outlet_branch'] ?? $outlet->outlet_branch,
            'outlet_email' => $validated['outlet_email'] ?? $outlet->outlet_email,
            'outlet_phone' => $validated['outlet_phone'] ?? $outlet->outlet_phone,
            'outlet_address' => $validated['outlet_address'] ?? $outlet->outlet_address,
            'updated_by' => 'admin',
        ];

        if ($request->hasFile('outlet_image')) {
            if ($outlet->outlet_image && Storage::disk('public')->exists($outlet->outlet_image)) {
                Storage::disk('public')->delete($outlet->outlet_image);
            }
            $path = $request->file('outlet_image')->store('outlets', 'public');
            $updateData['outlet_image'] = $path;
        }

        $outlet->update($updateData);

        // Sinkronisasi nama outlet ke setting_outlets
        SettingOutlet::updateOrCreate(
            ['outlet_id' => $outlet->outlet_id],
            ['outlet_name' => $outlet->outlet_name, 'updated_by' => 'admin']
        );

        return response()->json([
            'success' => true,
            'message' => 'Informasi profil usaha berhasil diperbarui.',
            'outlet_name' => $outlet->outlet_name,
            'image_url' => $outlet->outlet_image ? asset('storage/' . $outlet->outlet_image) : null,
        ]);
    }

    /**
     * Beralih cabang outlet aktif untuk sesi kasir/admin.
     */
    public function switchOutlet(Request $request)
    {
        $request->validate([
            'outlet_id' => 'required|string',
        ]);

        $outlet = Outlet::where('outlet_id', $request->outlet_id)
            ->where('delete_status', 0)
            ->first();

        if ($outlet) {
            session([
                'active_outlet_id' => $outlet->outlet_id,
                'outlet_id' => $outlet->outlet_id,
                'active_outlet_name' => $outlet->outlet_name,
            ]);

            return back()->with('success', "Berhasil beralih ke cabang: {$outlet->outlet_name}");
        }

        return back()->with('error', 'Cabang outlet tidak ditemukan.');
    }

    /**
     * Tampilkan form halaman baru untuk menambah cabang baru di panel POS Admin.
     */
    public function createOutlet()
    {
        $currentOutlets = Outlet::where('delete_status', 0)->get();
        $suggestedBrand = session('client_name') ?? session('business_name') ?? 'Outlet';

        return view('admin.setting.create_outlet', [
            'currentOutlets' => $currentOutlets,
            'suggestedBrand' => $suggestedBrand,
        ]);
    }

    /**
     * Simpan cabang baru langsung di database client.
     */
    public function storeOutlet(Request $request)
    {
        $validated = $request->validate([
            'outlet_name' => 'required|string|max:150',
            'outlet_code' => 'nullable|string|max:20',
            'outlet_branch' => 'nullable|string|max:100',
            'outlet_phone' => 'nullable|string|max:30',
            'outlet_email' => 'nullable|email|max:100',
            'outlet_address' => 'nullable|string|max:500',
            'outlet_status' => 'nullable|in:0,1',
        ]);

        $outletId = (string) \Illuminate\Support\Str::ulid();
        $outletSlug = \Illuminate\Support\Str::slug($validated['outlet_name']) . '-' . \Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(4));

        $outlet = Outlet::create([
            'outlet_id' => $outletId,
            'outlet_name' => $validated['outlet_name'],
            'outlet_code' => $validated['outlet_code'] ?? strtoupper(\Illuminate\Support\Str::random(4)),
            'outlet_branch' => $validated['outlet_branch'] ?? 'Cabang',
            'outlet_slug' => $outletSlug,
            'outlet_phone' => $validated['outlet_phone'] ?? null,
            'outlet_email' => $validated['outlet_email'] ?? null,
            'outlet_address' => $validated['outlet_address'] ?? null,
            'outlet_status' => $validated['outlet_status'] ?? 1,
            'created_by' => auth()->user()?->name ?? 'Admin',
        ]);

        // Auto-inisialisasi setting outlet
        SettingOutlet::create([
            'outlet_id' => $outletId,
            'outlet_name' => $validated['outlet_name'],
            'payment_timing' => 'post_payment',
            'theme' => config('app.guest_template', 'spicy_bites'),
            'created_by' => auth()->user()?->name ?? 'Admin',
        ]);

        // Auto-inisialisasi shift settings
        ShiftSetting::create([
            'outlet_id' => $outletId,
            'daily_cutoff_time' => '03:00:00',
            'shift_mode' => 'auto_master',
            'auto_lock_unclosed' => 1,
        ]);

        // Auto-inisialisasi Pajak & Service Charge
        Tax::create([
            'outlet_id' => $outletId,
            'tax_name' => 'PB1 Restoran (10%)',
            'rate_percent' => 10.00,
            'type' => 'exclusive',
            'is_active' => 1,
            'created_by' => auth()->user()?->name ?? 'Admin',
        ]);

        ServiceCharge::create([
            'outlet_id' => $outletId,
            'service_name' => 'Service Charge (5%)',
            'rate_percent' => 5.00,
            'is_taxable' => 1,
            'is_active' => 1,
            'created_by' => auth()->user()?->name ?? 'Admin',
        ]);

        // Otomatis aktifkan sesi ke cabang yang baru dibuat
        session([
            'active_outlet_id' => $outlet->outlet_id,
            'outlet_id' => $outlet->outlet_id,
            'active_outlet_name' => $outlet->outlet_name,
        ]);

        return redirect()->route('admin.setting.index')
            ->with('success', "Cabang '{$outlet->outlet_name}' berhasil ditambahkan dan langsung aktif!");
    }
}
