<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin') — Kasir POS</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('nexora-assets/css/main.css') }}">
@stack('styles')
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="app-shell">

  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar" id="appSidebar">
    <div class="sidebar-brand">
      <div class="brand-mark">K</div>
      <span class="brand-name">Kasir POS</span>
    </div>

    <nav class="sidebar-nav scroll-thin">
      <div class="nav-section-title">Master Data</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'stock') active @endif">
          <a href="{{ route('admin.stock.index') }}" class="nav-link"><i class="bi bi-box-seam"></i><span class="nav-label-text">Stok Bahan</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'supplier') active @endif">
          <a href="{{ route('admin.supplier.index') }}" class="nav-link"><i class="bi bi-truck"></i><span class="nav-label-text">Supplier</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Transaksi</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'order') active @endif">
          <a href="{{ route('admin.order.index') }}" class="nav-link"><i class="bi bi-bag-fill"></i><span class="nav-label-text">Pesan</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'order-list') active @endif">
          <a href="{{ route('admin.order.list') }}" class="nav-link"><i class="bi bi-receipt"></i><span class="nav-label-text">Daftar Pesanan (Konfirmasi Order)</span></a>
        </li>

        <li class="nav-item @if(($activeMenu ?? '') === 'transaction') active @endif">
          <a href="{{ route('admin.transaction.index') }}" class="nav-link"><i class="bi bi-credit-card"></i><span class="nav-label-text">Transaksi</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Sample Menu</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'category') active @endif">
          <a href="{{ route('admin.category.index') }}" class="nav-link"><i class="bi bi-tags-fill"></i><span class="nav-label-text">Kategori</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'product') active @endif">
          <a href="{{ route('admin.product.index') }}" class="nav-link"><i class="bi bi-cup-hot-fill"></i><span class="nav-label-text">Produk</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'bundle') active @endif">
          <a href="{{ route('admin.bundle.index') }}" class="nav-link"><i class="bi bi-gift"></i><span class="nav-label-text">Paket Bundle</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'table') active @endif">
          <a href="{{ route('admin.table.index') }}" class="nav-link"><i class="bi bi-grid-3x3-gap-fill"></i><span class="nav-label-text">Meja</span></a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-people-fill"></i><span class="nav-label-text">Pelanggan</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Promo</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'discount') active @endif">
          <a href="{{ route('admin.discount.index') }}" class="nav-link"><i class="bi bi-percent"></i><span class="nav-label-text">Diskon</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'voucher') active @endif">
          <a href="{{ route('admin.voucher.index') }}" class="nav-link"><i class="bi bi-ticket-perforated"></i><span class="nav-label-text">Voucher</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Keuangan & Setting</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'setting' || ($activeMenu ?? '') === 'setting-tax' || ($activeMenu ?? '') === 'setting-shift') active @endif">
          <a href="{{ route('admin.setting.index') }}" class="nav-link"><i class="bi bi-gear-fill"></i><span class="nav-label-text">Setting</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'shift-operational') active @endif">
          <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="nav-link"><i class="bi bi-person-badge-fill" style="color: #4ade80;"></i><span class="nav-label-text">Buka / Tutup Shift (Clock-In)</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'cogs-raw-material') active @endif">


          <a href="{{ route('admin.keuangan.cogs-raw-material.index') }}" class="nav-link"><i class="bi bi-box-seam"></i><span class="nav-label-text">Bahan Mentah COGS</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'purchase-order') active @endif">
          <a href="{{ route('admin.keuangan.purchase-order.index') }}" class="nav-link"><i class="bi bi-cart-plus"></i><span class="nav-label-text">Purchase Order (PO)</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'cogs-recipe') active @endif">
          <a href="{{ route('admin.keuangan.cogs-recipe.index') }}" class="nav-link"><i class="bi bi-book"></i><span class="nav-label-text">Resep & COGS Menu</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'cogs-waste') active @endif">
          <a href="{{ route('admin.keuangan.cogs-waste.index') }}" class="nav-link"><i class="bi bi-trash3"></i><span class="nav-label-text">Bahan Terbuang (Waste Log)</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Laporan & Analytics</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'reports-dashboard') active @endif">
          <a href="{{ route('admin.reports.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2"></i><span class="nav-label-text">Pusat Dashboard Laporan</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'reports-sales') active @endif">
          <a href="{{ route('admin.reports.sales') }}" class="nav-link"><i class="bi bi-credit-card-2-front"></i><span class="nav-label-text">Laporan Penjualan</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'reports-products') active @endif">
          <a href="{{ route('admin.reports.products') }}" class="nav-link"><i class="bi bi-cup-hot"></i><span class="nav-label-text">Performa Menu Terlaris</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'reports-cashflow') active @endif">
          <a href="{{ route('admin.reports.cashflow') }}" class="nav-link"><i class="bi bi-cash-stack"></i><span class="nav-label-text">Laporan Arus Kas</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'reports-tax-service') active @endif">
          <a href="{{ route('admin.reports.tax-service') }}" class="nav-link"><i class="bi bi-bank"></i><span class="nav-label-text">Laporan Pajak & Service</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'reports-inventory') active @endif">
          <a href="{{ route('admin.reports.inventory') }}" class="nav-link"><i class="bi bi-boxes"></i><span class="nav-label-text">Laporan Stok & Waste</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'reports-shifts') active @endif">
          <a href="{{ route('admin.reports.shifts') }}" class="nav-link"><i class="bi bi-shield-lock"></i><span class="nav-label-text">Audit Shift Closing Kasir</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'hpp-report') active @endif">
          <a href="{{ route('admin.keuangan.hpp-report.index') }}" class="nav-link"><i class="bi bi-graph-up-arrow"></i><span class="nav-label-text">Laporan HPP & Laba Rugi</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'menu-analytics') active @endif">
          <a href="{{ route('admin.keuangan.menu-analytics.index') }}" class="nav-link"><i class="bi bi-pie-chart-fill"></i><span class="nav-label-text">Grafik Analitik Menu</span></a>
        </li>
      </ul>


      <div class="nav-section-title">Riwayat</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'history-stock') active @endif">
          <a href="{{ route('admin.history.stock.index') }}" class="nav-link"><i class="bi bi-archive"></i><span class="nav-label-text">Riwayat Stok</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'history-product') active @endif">
          <a href="{{ route('admin.history.product.index') }}" class="nav-link"><i class="bi bi-cup-hot"></i><span class="nav-label-text">Riwayat Produk</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'history-discount') active @endif">
          <a href="{{ route('admin.history.discount.index') }}" class="nav-link"><i class="bi bi-percent"></i><span class="nav-label-text">Riwayat Diskon</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'history-voucher') active @endif">
          <a href="{{ route('admin.history.voucher.index') }}" class="nav-link"><i class="bi bi-ticket-perforated"></i><span class="nav-label-text">Riwayat Voucher</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'history-bundle') active @endif">
          <a href="{{ route('admin.history.bundle.index') }}" class="nav-link"><i class="bi bi-gift"></i><span class="nav-label-text">Riwayat Bundle</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Bantuan & Documentation</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'guide' || ($activeMenu ?? '') === 'manual-book') active @endif">
          <a href="{{ route('admin.manual-book.index') }}" class="nav-link">
            <i class="bi bi-journal-bookmark-fill" style="color: #60a5fa;"></i>
            <span class="nav-label-text">Manual Book (Panduan System)</span>
          </a>
        </li>
      </ul>

    </nav>
  </aside>

  <!-- ============ MAIN COLUMN ============ -->
  <div class="main-col">

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
        <button class="icon-btn" id="themeToggleBtn" aria-label="Ganti tema">
          <i class="bi bi-sun"></i>
        </button>
        <button class="icon-btn" aria-label="Notifikasi">
          <i class="bi bi-bell-fill"></i>
          <span class="dot-badge"></span>
        </button>
        <div class="dropdown">
          <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="user-avatar">AD</div>
            <div class="d-none d-md-block">
              <div class="user-chip-name">Admin</div>
              <div class="user-chip-role">Administrator</div>
            </div>
            <i class="bi bi-chevron-down" style="font-size:0.7rem; color:var(--text-muted);"></i>
          </div>
          <ul class="dropdown-menu dropdown-menu-end mt-2">
            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
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
