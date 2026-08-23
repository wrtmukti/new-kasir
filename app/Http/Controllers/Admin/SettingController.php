<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\SettingOutlet;
use App\Models\Admin\Tax;
use App\Models\Admin\ServiceCharge;
use App\Models\Admin\ShiftSetting;
use App\Models\Admin\Shift;
use App\Models\SysAdmin\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman utama Setting Outlet & Kasir.
     */
    public function index()
    {
        $company = Company::where('delete_status', 0)->first();
        if (!$company) {
            $company = Company::create([
                'company_name' => 'Restoran & Cafe',
                'company_code' => 'RESTO',
                'company_branch' => 'Pusat',
                'created_by' => 'admin',
            ]);
        }

        $setting = SettingOutlet::where('company_id', $company->company_id)
            ->where('delete_status', 0)
            ->first();

        if (!$setting) {
            $setting = SettingOutlet::create([
                'company_id' => $company->company_id,
                'outlet_name' => $company->company_name,
                'payment_timing' => 'post_payment',
                'theme' => config('app.guest_template', 'spicy_bites'),
                'created_by' => 'admin',
            ]);
        }

        // Data Master Pajak & Service Charge
        $tax = Tax::where('is_active', 1)->first() ?? Tax::first();
        $service = ServiceCharge::where('is_active', 1)->first() ?? ServiceCharge::first();

        // Data Master Shift & Cut-Off
        $companyId = session('company_id') ?? $company->company_id ?? 'COMP-001';
        $shiftSetting = ShiftSetting::where('company_id', $companyId)->first() 
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

        return view('admin.setting.index', compact('company', 'setting', 'themes', 'tax', 'service', 'shiftSetting', 'shifts'));
    }

    /**
     * Update Pengaturan Alur Pembayaran (Bayar di Awal vs Bayar di Akhir).
     */
    public function updatePaymentSetting(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payment_timing' => 'required|in:post_payment,pre_payment',
        ]);

        $company = Company::where('delete_status', 0)->first();
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Perusahaan tidak ditemukan.'], 404);
        }

        $setting = SettingOutlet::firstOrCreate(
            ['company_id' => $company->company_id],
            ['created_by' => 'admin']
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

        $company = Company::where('delete_status', 0)->first();
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Perusahaan tidak ditemukan.'], 404);
        }

        $setting = SettingOutlet::firstOrCreate(
            ['company_id' => $company->company_id],
            ['created_by' => 'admin']
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
            'company_name' => 'required|string|max:150',
            'company_code' => 'nullable|string|max:20',
            'company_branch' => 'nullable|string|max:100',
            'company_email' => 'nullable|email|max:100',
            'company_phone' => 'nullable|string|max:30',
            'company_address' => 'nullable|string|max:500',
            'company_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ]);

        $company = Company::where('delete_status', 0)->first();
        if (!$company) {
            $company = Company::create([
                'company_name' => $validated['company_name'],
                'created_by' => 'admin',
            ]);
        }

        $updateData = [
            'company_name' => $validated['company_name'],
            'company_code' => $validated['company_code'] ?? $company->company_code,
            'company_branch' => $validated['company_branch'] ?? $company->company_branch,
            'company_email' => $validated['company_email'] ?? $company->company_email,
            'company_phone' => $validated['company_phone'] ?? $company->company_phone,
            'company_address' => $validated['company_address'] ?? $company->company_address,
            'updated_by' => 'admin',
        ];

        if ($request->hasFile('company_image')) {
            if ($company->company_image && Storage::disk('public')->exists($company->company_image)) {
                Storage::disk('public')->delete($company->company_image);
            }
            $path = $request->file('company_image')->store('companies', 'public');
            $updateData['company_image'] = $path;
        }

        $company->update($updateData);

        // Sinkronisasi nama outlet ke setting_outlets
        SettingOutlet::updateOrCreate(
            ['company_id' => $company->company_id],
            ['outlet_name' => $company->company_name, 'updated_by' => 'admin']
        );

        return response()->json([
            'success' => true,
            'message' => 'Informasi profil usaha berhasil diperbarui.',
            'company_name' => $company->company_name,
            'image_url' => $company->company_image ? asset('storage/' . $company->company_image) : null,
        ]);
    }
}
