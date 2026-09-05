<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin') — {{ request()->routeIs('owner.*') ? 'Portal Owner' : (session('active_outlet_name') ?? session('business_name') ?? 'Kasir POS') }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('nexora-assets/css/main.css') }}">
<script>
  (function() {
    const saved = localStorage.getItem('nexora-theme') || 'dark';
    document.documentElement.setAttribute('data-theme', saved);
  })();
</script>
<style>
  .topbar-actions {
    display: flex;
    align-items: center;
    gap: 0.65rem;
  }
  .outlet-select-card:hover {
    background: var(--bg-elevated-2) !important;
    border-color: rgba(99, 102, 241, 0.4) !important;
  }
  [data-theme="light"] .outlet-select-card:hover {
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
  }
</style>
@stack('styles')
</head>
<body>

@php
  $isOwnerPortal = request()->routeIs('owner.*');
  $userRole = auth()->user()?->role ?? 'kasir';
  $isOwnerUser = ($userRole === 'admin');
  $availableOutlets = \App\Models\Admin\Outlet::where('delete_status', 0)->where('outlet_status', 1)->orderBy('outlet_name')->get();
  $activeOutletId = session('active_outlet_id') ?? session('outlet_id') ?? ($availableOutlets->first()?->outlet_id ?? '');
  $currentOutlet = $availableOutlets->firstWhere('outlet_id', $activeOutletId) ?? $availableOutlets->first();
@endphp

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="app-shell">

  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar" id="appSidebar">
    @if($isOwnerPortal)
      {{-- BRAND HEADER PORTAL OWNER --}}
      <div class="sidebar-brand d-flex align-items-center gap-2.5">
        <div class="brand-mark fw-bold">
          <i class="bi bi-crown-fill" style="font-size: 1.1rem;"></i>
        </div>
        <div class="d-flex flex-column text-truncate" style="line-height: 1.25;">
          <span class="brand-name text-truncate fw-bold text-primary" style="font-size: 0.95rem;">
            PORTAL OWNER
          </span>
          <div class="d-flex align-items-center gap-1 mt-0.5">
            <span class="text-muted-c text-truncate fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.2px;" title="{{ session('client_name') ?? session('business_name') ?? 'Kasir POS' }}">
              {{ session('client_name') ?? session('business_name') ?? 'Kasir POS' }}
            </span>
          </div>
        </div>
      </div>
    @else
      {{-- BRAND HEADER CABANG OPERASIONAL --}}
      <div class="sidebar-brand d-flex align-items-center gap-2.5">
        <div class="brand-mark fw-bold">{{ strtoupper(substr($currentOutlet?->outlet_name ?? session('client_name') ?? 'K', 0, 1)) }}</div>
        <div class="d-flex flex-column text-truncate" style="line-height: 1.25;">
          <span class="brand-name text-truncate fw-bold text-primary" style="font-size: 0.95rem;" title="{{ $currentOutlet?->outlet_name ?? 'Cabang Utama' }}">
            {{ $currentOutlet?->outlet_name ?? 'Cabang Utama' }}
          </span>
          <div class="d-flex align-items-center gap-1 mt-0.5">
            <span class="text-muted-c text-truncate fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.2px;" title="{{ session('client_name') ?? session('business_name') ?? 'Kasir POS' }}">
              {{ session('client_name') ?? session('business_name') ?? 'Kasir POS' }}
            </span>
            @if(session('client_code'))
              <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 0.62rem; padding: 1px 4px;">
                {{ session('client_code') }}
              </span>
            @endif
          </div>
        </div>
      </div>
    @endif

    <nav class="sidebar-nav scroll-thin">
      @if($isOwnerPortal)
        {{-- ================= MENU KHUSUS PORTAL OWNER ================= --}}
        <div class="nav-section-title">Owner Menu</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(($activeMenu ?? '') === 'owner-dashboard') active @endif">
            <a href="{{ route('owner.dashboard') }}" class="nav-link">
              <i class="bi bi-grid-fill"></i>
              <span class="nav-label-text">Dashboard</span>
            </a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'owner-branches') active @endif">
            <a href="{{ route('owner.branches.index') }}" class="nav-link">
              <i class="bi bi-buildings-fill"></i>
              <span class="nav-label-text">Manajemen Cabang</span>
            </a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'owner-financial') active @endif">
            <a href="{{ route('owner.financial') }}" class="nav-link">
              <i class="bi bi-pie-chart-fill"></i>
              <span class="nav-label-text">Laba Rugi &amp; Cash Flow</span>
            </a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'owner-benchmark') active @endif">
            <a href="{{ route('owner.benchmark') }}" class="nav-link">
              <i class="bi bi-trophy-fill"></i>
              <span class="nav-label-text">Leaderboard &amp; Benchmark</span>
            </a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'owner-audit') active @endif">
            <a href="{{ route('owner.audit') }}" class="nav-link">
              <i class="bi bi-shield-exclamation"></i>
              <span class="nav-label-text">Audit Selisih &amp; Waste</span>
            </a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'owner-cash-debt') active @endif">
            <a href="{{ route('owner.cash-debt') }}" class="nav-link">
              <i class="bi bi-wallet2"></i>
              <span class="nav-label-text">Setoran &amp; Hutang PO</span>
            </a>
          </li>
        </ul>

      @else
        {{-- ================= MENU TOKO CABANG OPERASIONAL (ADMIN & KASIR) ================= --}}
        @if($isOwnerUser)
          <div class="p-2 mb-2">
            <a href="{{ route('owner.dashboard') }}" class="btn btn-sm w-100 fw-bold d-flex align-items-center justify-content-center gap-1.5 py-2 rounded-3 shadow-sm"
               style="background: rgba(99, 102, 241, 0.12); border: 1px solid rgba(99, 102, 241, 0.35); color: var(--accent-1); font-size: 0.78rem;">
              <i class="bi bi-crown-fill"></i>
              <span>Kembali ke Portal Owner</span>
            </a>
          </div>
        @endif

        <div class="nav-section-title">Main Toko</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(($activeMenu ?? '') === 'dashboard' || ($activeMenu ?? '') === 'menu-analytics') active @endif">
            <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2"></i><span class="nav-label-text">Dashboard Toko</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'shift-operational') active @endif">
            <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="nav-link"><i class="bi bi-person-badge-fill"></i><span class="nav-label-text">Clock In / Shift</span></a>
          </li>
        </ul>

        <div class="nav-section-title">Transaksi Toko</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(($activeMenu ?? '') === 'order') active @endif">
            <a href="{{ route('admin.order.index') }}" class="nav-link"><i class="bi bi-bag-fill"></i><span class="nav-label-text">Kasir POS</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'order-list') active @endif">
            <a href="{{ route('admin.order.list') }}" class="nav-link"><i class="bi bi-receipt"></i><span class="nav-label-text">Daftar Pesanan</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'transaction') active @endif">
            <a href="{{ route('admin.transaction.index') }}" class="nav-link"><i class="bi bi-credit-card"></i><span class="nav-label-text">Riwayat Transaksi</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'table') active @endif">
            <a href="{{ route('admin.table.index') }}" class="nav-link"><i class="bi bi-grid-3x3-gap-fill"></i><span class="nav-label-text">Meja Resto</span></a>
          </li>
        </ul>

        <div class="nav-section-title">Master Data Cabang</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(($activeMenu ?? '') === 'stock') active @endif">
            <a href="{{ route('admin.stock.index') }}" class="nav-link"><i class="bi bi-box-seam"></i><span class="nav-label-text">Stok Bahan</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'product') active @endif">
            <a href="{{ route('admin.product.index') }}" class="nav-link"><i class="bi bi-cup-hot-fill"></i><span class="nav-label-text">Produk</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'category') active @endif">
            <a href="{{ route('admin.category.index') }}" class="nav-link"><i class="bi bi-tags-fill"></i><span class="nav-label-text">Kategori Menu</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'bundle') active @endif">
            <a href="{{ route('admin.bundle.index') }}" class="nav-link"><i class="bi bi-gift"></i><span class="nav-label-text">Paket Bundle</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'supplier') active @endif">
            <a href="{{ route('admin.supplier.index') }}" class="nav-link"><i class="bi bi-truck"></i><span class="nav-label-text">Supplier</span></a>
          </li>
        </ul>

        <div class="nav-section-title">Promo Toko</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(($activeMenu ?? '') === 'discount') active @endif">
            <a href="{{ route('admin.discount.index') }}" class="nav-link"><i class="bi bi-percent"></i><span class="nav-label-text">Diskon</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'voucher') active @endif">
            <a href="{{ route('admin.voucher.index') }}" class="nav-link"><i class="bi bi-ticket-perforated"></i><span class="nav-label-text">Voucher</span></a>
          </li>
        </ul>

        <div class="nav-section-title">Keuangan Toko</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(($activeMenu ?? '') === 'cogs-raw-material') active @endif">
            <a href="{{ route('admin.keuangan.cogs-raw-material.index') }}" class="nav-link"><i class="bi bi-box-seam"></i><span class="nav-label-text">Bahan Mentah</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'purchase-order') active @endif">
            <a href="{{ route('admin.keuangan.purchase-order.index') }}" class="nav-link"><i class="bi bi-cart-plus"></i><span class="nav-label-text">Purchase Order</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'cogs-recipe') active @endif">
            <a href="{{ route('admin.keuangan.cogs-recipe.index') }}" class="nav-link"><i class="bi bi-book"></i><span class="nav-label-text">Resep & COGS Menu</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'cogs-waste') active @endif">
            <a href="{{ route('admin.keuangan.cogs-waste.index') }}" class="nav-link"><i class="bi bi-trash3"></i><span class="nav-label-text">Bahan Terbuang</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'financial-guide') active @endif">
            <a href="{{ route('admin.keuangan.financial-guide.index') }}" class="nav-link"><i class="bi bi-journal-bookmark"></i><span class="nav-label-text">Panduan Finansial</span></a>
          </li>
        </ul>

        <div class="nav-section-title">Laporan Toko</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(str_starts_with($activeMenu ?? '', 'reports-') || ($activeMenu ?? '') === 'hpp-report') active @endif">
            <a href="{{ route('admin.reports.dashboard') }}" class="nav-link"><i class="bi bi-file-earmark-bar-graph"></i><span class="nav-label-text">Laporan Cabang</span></a>
          </li>
        </ul>

        <div class="nav-section-title">Pengaturan Toko</div>
        <ul class="list-unstyled">
          <li class="nav-item @if(($activeMenu ?? '') === 'setting' || ($activeMenu ?? '') === 'setting-tax' || ($activeMenu ?? '') === 'setting-shift') active @endif">
            <a href="{{ route('admin.setting.index') }}" class="nav-link"><i class="bi bi-gear-fill"></i><span class="nav-label-text">Setting Toko</span></a>
          </li>
          <li class="nav-item @if(str_starts_with($activeMenu ?? '', 'history-') || ($activeMenu ?? '') === 'history') active @endif">
            <a href="{{ route('admin.history.index') }}" class="nav-link"><i class="bi bi-clock-history"></i><span class="nav-label-text">Log Perubahan</span></a>
          </li>
          <li class="nav-item @if(($activeMenu ?? '') === 'guide' || ($activeMenu ?? '') === 'manual-book') active @endif">
            <a href="{{ route('admin.manual-book.index') }}" class="nav-link"><i class="bi bi-journal-bookmark-fill"></i><span class="nav-label-text">Panduan Sistem</span></a>
          </li>
        </ul>
      @endif

    </nav>
  </aside>

  <!-- ============ MAIN COLUMN ============ -->
  <div class="main-col">

    @if(session('is_impersonating'))
      <div class="px-4 py-2 d-flex align-items-center justify-content-between text-white shadow-sm" style="background: linear-gradient(90deg, #d97706, #b45309); font-size:0.85rem; z-index: 1050;">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-shield-lock-fill fs-5"></i>
          <span>
            <strong>Mode Impersonation:</strong> Anda sedang login sebagai client <strong>{{ session('impersonated_client_name') }}</strong> (Diakses oleh System Admin: <strong>{{ session('impersonator_name') }}</strong>).
          </span>
        </div>
        <a href="{{ route('sys_admin.impersonate.stop') }}" class="btn btn-sm btn-light text-dark fw-bold rounded-pill px-3 py-1 shadow-sm">
          <i class="bi bi-box-arrow-left me-1"></i>Kembali ke System Admin
        </a>
      </div>
    @endif

    <!-- TOPBAR -->
    <header class="topbar">
      <button class="sidebar-toggle-btn d-none d-lg-flex" id="sidebarCollapseBtn" aria-label="Lipat sidebar">
        <i class="bi bi-layout-sidebar-inset"></i>
      </button>
      <button class="sidebar-toggle-btn d-lg-none" id="sidebarMobileToggle" aria-label="Buka menu">
        <i class="bi bi-list"></i>
      </button>

      <div class="topbar-search">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Cari...">
        <span class="kbd-hint">⌘K</span>
      </div>

      <div class="topbar-actions">
        <!-- MODERN OUTLET & PORTAL SWITCHER WIDGET -->
        @if($isOwnerUser)
          {{-- OWNER: BISA BERALIH ANTARA PORTAL OWNER & TOKO CABANG --}}
          <div class="dropdown">
            <button class="btn d-flex align-items-center px-3 py-1.5 rounded-3 border-0 shadow-sm" 
                    type="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false" 
                    style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; transition: all 0.2s ease;">
              @if($isOwnerPortal)
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 32px; height: 32px; margin-right: 10px; background: rgba(99, 102, 241, 0.12); color: var(--brand-primary, #6366f1); font-size: 0.95rem;">
                  <i class="bi bi-crown-fill"></i>
                </div>
                <div class="d-none d-sm-flex flex-column text-start" style="line-height: 1.2; margin-right: 8px;">
                  <span class="text-muted-c fw-semibold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.4px;">Portal Owner</span>
                  <span class="fw-bold text-truncate" style="font-size: 0.82rem; color: var(--text-primary); max-width: 140px;" title="{{ session('client_name') ?? 'Semua Cabang' }}">
                    {{ session('client_name') ?? 'Semua Cabang' }}
                  </span>
                </div>
                <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold px-2 py-0.5 d-none d-md-inline-block" style="font-size: 0.65rem; margin-right: 4px;">
                  Owner
                </span>
              @else
                <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 32px; height: 32px; margin-right: 10px; background: rgba(99, 102, 241, 0.12); color: var(--brand-primary, #6366f1); font-size: 0.92rem;">
                  <i class="bi bi-shop-window"></i>
                </div>
                <div class="d-none d-sm-flex flex-column text-start" style="line-height: 1.2; margin-right: 8px;">
                  <span class="text-muted-c fw-semibold text-uppercase" style="font-size: 0.62rem; letter-spacing: 0.4px;">Cabang Aktif</span>
                  <span class="fw-bold text-truncate" style="font-size: 0.82rem; color: var(--text-primary); max-width: 140px;">
                    {{ $currentOutlet?->outlet_name ?? 'Pilih Cabang' }}
                  </span>
                </div>
                <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold px-2 py-0.5 d-none d-md-inline-block" style="font-size: 0.65rem; margin-right: 4px;">
                  {{ $currentOutlet?->outlet_branch ?? 'Utama' }}
                </span>
              @endif
              <i class="bi bi-chevron-expand text-muted-c ms-1" style="font-size: 0.72rem;"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end shadow-lg rounded-4 p-0 overflow-hidden border-0 mt-2" 
                 style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; min-width: 320px; z-index: 1060;">
              
              <!-- SECTION 1: PORTAL OWNER -->
              <div class="p-2.5 border-bottom" style="border-color: var(--border-subtle) !important; background: var(--bg-elevated-2);">
                <a href="{{ route('owner.dashboard') }}" 
                   class="d-flex align-items-center justify-content-between p-2 rounded-3 text-decoration-none"
                   style="background: {{ $isOwnerPortal ? 'rgba(99, 102, 241, 0.12)' : 'transparent' }}; border: 1px solid {{ $isOwnerPortal ? 'rgba(99, 102, 241, 0.4)' : 'transparent' }}; transition: all 0.15s ease; color: inherit;">
                  <div class="d-flex align-items-center min-w-0">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 36px; height: 36px; margin-right: 10px; background: {{ $isOwnerPortal ? 'linear-gradient(135deg, #6366f1, #4f46e5)' : 'var(--bg-elevated-2)' }}; color: {{ $isOwnerPortal ? '#ffffff' : 'var(--text-muted)' }}; font-size: 1rem;">
                      <i class="bi bi-crown-fill"></i>
                    </div>
                    <div class="d-flex flex-column">
                      <span class="fw-bold" style="font-size: 0.84rem; color: var(--text-primary);">Portal Owner</span>
                      <small class="text-muted-c" style="font-size: 0.7rem;">Ringkasan performa seluruh cabang</small>
                    </div>
                  </div>
                  @if($isOwnerPortal)
                    <span class="badge bg-primary text-white fw-semibold px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">Aktif</span>
                  @else
                    <span class="badge bg-secondary-subtle text-muted-c fw-medium px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">Buka</span>
                  @endif
                </a>
              </div>

              <!-- SECTION 2: TOKO CABANG OPERASIONAL -->
              <div class="px-3 pt-2 pb-1 d-flex align-items-center justify-content-between">
                <span class="text-uppercase text-muted-c fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Toko Cabang Operasional</span>
                <span class="badge bg-secondary-subtle text-secondary fw-semibold rounded-pill px-2 py-0.5" style="font-size: 0.62rem;">
                  {{ $availableOutlets->count() }} Cabang
                </span>
              </div>

              <div class="p-2 scroll-thin" style="max-height: 220px; overflow-y: auto;">
                @foreach($availableOutlets as $out)
                  @php $isActive = (!$isOwnerPortal && $activeOutletId == $out->outlet_id); @endphp
                  <a href="{{ route('admin.switch-outlet', ['outlet_id' => $out->outlet_id, 'redirect_to' => ($isOwnerPortal ? 'store' : '')]) }}" 
                     class="d-flex align-items-center justify-content-between p-2 rounded-3 mb-1 text-decoration-none"
                     style="background: {{ $isActive ? 'rgba(99, 102, 241, 0.1)' : 'transparent' }}; border: 1px solid {{ $isActive ? 'rgba(99, 102, 241, 0.35)' : 'transparent' }}; transition: all 0.15s ease; color: inherit;">
                    <div class="d-flex align-items-center min-w-0">
                      <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                           style="width: 36px; height: 36px; margin-right: 10px; background: {{ $isActive ? 'linear-gradient(135deg, #6366f1, #4f46e5)' : 'var(--bg-elevated-2)' }}; color: {{ $isActive ? '#ffffff' : 'var(--text-muted)' }}; font-size: 0.95rem;">
                        <i class="bi bi-shop"></i>
                      </div>
                      <div class="d-flex flex-column text-truncate" style="padding-left: 2px;">
                        <span class="fw-bold text-truncate" style="font-size: 0.82rem; color: {{ $isActive ? 'var(--brand-primary, #6366f1)' : 'var(--text-primary)' }};">
                          {{ $out->outlet_name }}
                        </span>
                        <div class="d-flex align-items-center gap-1.5 text-muted-c" style="font-size: 0.7rem; margin-top: 1px;">
                          <i class="bi bi-geo-alt" style="font-size: 0.65rem;"></i>
                          <span class="text-truncate">{{ $out->outlet_branch ?? 'Cabang Pusat' }}</span>
                          @if($out->outlet_code)
                            <span>•</span>
                            <span>{{ $out->outlet_code }}</span>
                          @endif
                        </div>
                      </div>
                    </div>

                    @if($isActive)
                      <div class="flex-shrink-0 ms-2">
                        <span class="badge bg-success-subtle text-success fw-bold d-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="font-size: 0.65rem;">
                          <i class="bi bi-check-circle-fill"></i> Aktif
                        </span>
                      </div>
                    @else
                      <div class="flex-shrink-0 ms-2">
                        <span class="badge bg-secondary-subtle text-muted-c fw-medium px-2 py-0.5 rounded-pill" style="font-size: 0.65rem;">
                          Masuk
                        </span>
                      </div>
                    @endif
                  </a>
                @endforeach
              </div>

              <!-- Footer: Tambah Cabang Baru -->
              <div class="p-2 border-top" style="border-color: var(--border-subtle) !important; background: var(--bg-elevated-2);">
                <a href="{{ route('admin.outlets.create') }}" class="btn btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-1.5 py-1.5 rounded-3" 
                   style="background: transparent; border: 1px dashed var(--brand-primary, #6366f1); color: var(--brand-primary, #6366f1); font-size: 0.78rem; transition: all 0.2s ease;">
                  <i class="bi bi-plus-circle-fill"></i>+ Tambah Cabang Outlet Baru
                </a>
              </div>
            </div>
          </div>
        @else
          {{-- KASIR: HANYA MENAMPILKAN CABANG TUGAS (TERKUNCI) --}}
          <div class="d-flex align-items-center px-3 py-1.5 rounded-3 border-0 shadow-sm" 
               style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
            <div class="rounded-2 d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 30px; height: 30px; margin-right: 8px; background: rgba(99, 102, 241, 0.12); color: var(--brand-primary, #6366f1); font-size: 0.88rem;">
              <i class="bi bi-shop-window"></i>
            </div>
            <div class="d-none d-sm-flex flex-column text-start" style="line-height: 1.2;">
              <span class="text-muted-c fw-semibold text-uppercase" style="font-size: 0.58rem; letter-spacing: 0.4px;">Cabang Tugas</span>
              <span class="fw-bold text-truncate" style="font-size: 0.8rem; color: var(--text-primary); max-width: 140px;">
                {{ $currentOutlet?->outlet_name ?? 'Cabang Utama' }}
              </span>
            </div>
          </div>
        @endif

        <button class="icon-btn" id="themeToggleBtn" aria-label="Ganti tema">
          <i class="bi bi-sun"></i>
        </button>
        <button class="icon-btn" aria-label="Notifikasi">
          <i class="bi bi-bell-fill"></i>
          <span class="dot-badge"></span>
        </button>
        <div class="dropdown">
          <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
            <div class="d-none d-md-block">
              <div class="user-chip-name">{{ auth()->user()->name ?? 'Admin POS' }}</div>
              <div class="user-chip-role">{{ ucfirst(auth()->user()->role ?? 'Kasir') }}</div>
            </div>
            <i class="bi bi-chevron-down" style="font-size:0.7rem; color:var(--text-muted);"></i>
          </div>
          <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm rounded-3 border-0" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; min-width: 240px;">
            <li class="px-3 py-2 border-bottom" style="border-color: var(--border-subtle) !important;">
              <small class="text-muted-c d-block" style="font-size:0.75rem;">Login sebagai:</small>
              <div class="fw-semibold text-truncate" style="font-size:0.85rem; color:var(--text-primary);">{{ auth()->user()->email ?? 'admin@gmail.com' }}</div>
              <div class="d-flex align-items-center gap-1 mt-1 text-muted-c" style="font-size: 0.72rem;">
                <i class="bi bi-shop text-primary"></i>
                <span class="text-truncate">{{ $currentOutlet?->outlet_name ?? 'Cabang Utama' }}</span>
              </div>
            </li>

            @if($isOwnerUser)
              <li><a class="dropdown-item py-2" href="{{ route('owner.dashboard') }}"><i class="bi bi-crown me-2"></i>Portal Owner</a></li>
              <li><a class="dropdown-item py-2" href="{{ route('admin.setting.index') }}"><i class="bi bi-gear me-2"></i>Pengaturan Outlet</a></li>
            @endif
            <li><a class="dropdown-item py-2" href="{{ route('admin.manual-book.index') }}"><i class="bi bi-journal-bookmark me-2"></i>Panduan Sistem</a></li>
            <li><hr class="dropdown-divider my-1" style="border-color: var(--border-subtle);"></li>
            <li>
              <form action="{{ route('logout') }}" method="POST" id="logoutFormAdmin">
                @csrf
                <button type="submit" class="dropdown-item text-danger py-2" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem POS?')">
                  <i class="bi bi-box-arrow-right me-2"></i>Keluar (Logout)
                </button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="page-content">
      @yield('content')
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="{{ asset('nexora-assets/js/index.js') }}"></script>
@stack('scripts')
</body>
</html>
