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
        <li class="nav-item @if(($activeMenu ?? '') === 'purchase-order') active @endif">
          <a href="{{ route('admin.purchase-order.index') }}" class="nav-link"><i class="bi bi-cart-plus"></i><span class="nav-label-text">Purchase Order</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Sample Menu</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-tags-fill"></i><span class="nav-label-text">Kategori</span></a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-cup-hot-fill"></i><span class="nav-label-text">Produk</span></a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-people-fill"></i><span class="nav-label-text">Pelanggan</span></a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-receipt"></i><span class="nav-label-text">Transaksi</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Laporan</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-graph-up-arrow"></i><span class="nav-label-text">Analitik</span></a>
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
