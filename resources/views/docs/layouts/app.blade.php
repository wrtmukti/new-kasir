<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Nexora Admin') — Nexora Admin</title>
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
      <div class="brand-mark">N</div>
      <span class="brand-name">Nexora</span>
    </div>

    <nav class="sidebar-nav scroll-thin">
      <div class="nav-section-title">Overview</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'dashboard') active @endif">
          <a href="{{ url('docs/index') }}" class="nav-link"><i class="bi bi-grid-1x2-fill"></i><span class="nav-label-text">Dashboard</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'analytics') active @endif">
          <a href="{{ url('docs/analytics') }}" class="nav-link"><i class="bi bi-graph-up-arrow"></i><span class="nav-label-text">Analytics</span></a>
        </li>
      </ul>

      <div class="nav-section-title">AI Workspace</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-stars"></i><span class="nav-label-text">AI Models</span><span class="nav-badge">New</span></a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link" data-submenu-toggle>
            <i class="bi bi-chat-dots-fill"></i><span class="nav-label-text">Conversations</span>
            <i class="bi bi-chevron-right submenu-caret nav-label-text" style="margin-left:auto; font-size:0.7rem; transition:0.2s;"></i>
          </a>
          <ul class="nav-submenu" style="display:none;">
            <li class="nav-item"><a href="#" class="nav-link">All Chats</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Flagged</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Archived</a></li>
          </ul>
        </li>
      </ul>

      <div class="nav-section-title">UI Components</div>
      <ul class="list-unstyled">
        <li class="nav-item">
          <a href="#" class="nav-link" data-submenu-toggle>
            <i class="bi bi-grid-3x3-gap-fill"></i><span class="nav-label-text">All Components</span>
            <i class="bi bi-chevron-right submenu-caret nav-label-text" style="margin-left:auto; font-size:0.7rem; transition:0.2s;"></i>
          </a>
          <ul class="nav-submenu" style="display:none;">
            <li class="nav-item @if(($activeMenu ?? '') === 'ui-components') active @endif"><a href="{{ url('docs/ui-components') }}" class="nav-link">Showcase</a></li>
            <li class="nav-item @if(($activeMenu ?? '') === 'components') active @endif"><a href="{{ url('docs/components') }}" class="nav-link">Overview</a></li>
            <li class="nav-item @if(($activeMenu ?? '') === 'buttons') active @endif"><a href="{{ url('docs/buttons') }}" class="nav-link">Buttons</a></li>
            <li class="nav-item @if(($activeMenu ?? '') === 'cards') active @endif"><a href="{{ url('docs/cards') }}" class="nav-link">Cards</a></li>
            <li class="nav-item @if(($activeMenu ?? '') === 'forms') active @endif"><a href="{{ url('docs/forms') }}" class="nav-link">Forms</a></li>
            <li class="nav-item @if(($activeMenu ?? '') === 'tables') active @endif"><a href="{{ url('docs/tables') }}" class="nav-link">Tables</a></li>
            <li class="nav-item @if(($activeMenu ?? '') === 'charts') active @endif"><a href="{{ url('docs/charts') }}" class="nav-link">Charts</a></li>
            <li class="nav-item @if(($activeMenu ?? '') === 'icons') active @endif"><a href="{{ url('docs/icons') }}" class="nav-link">Icons</a></li>
          </ul>
        </li>
      </ul>

      <div class="nav-section-title">Management</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'users') active @endif">
          <a href="{{ url('docs/users') }}" class="nav-link"><i class="bi bi-people-fill"></i><span class="nav-label-text">Users</span></a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link"><i class="bi bi-credit-card-2-front-fill"></i><span class="nav-label-text">Billing</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'settings') active @endif">
          <a href="{{ url('docs/settings') }}" class="nav-link"><i class="bi bi-gear-fill"></i><span class="nav-label-text">Settings</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'profile') active @endif">
          <a href="{{ url('docs/profile') }}" class="nav-link"><i class="bi bi-person-circle"></i><span class="nav-label-text">Profile</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'pricing') active @endif">
          <a href="{{ url('docs/pricing') }}" class="nav-link"><i class="bi bi-currency-dollar"></i><span class="nav-label-text">Pricing</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'kanban') active @endif">
          <a href="{{ url('docs/kanban') }}" class="nav-link"><i class="bi bi-kanban-fill"></i><span class="nav-label-text">Kanban</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'invoice') active @endif">
          <a href="{{ url('docs/invoice') }}" class="nav-link"><i class="bi bi-receipt"></i><span class="nav-label-text">Invoice</span></a>
        </li>
      </ul>

      <div class="nav-section-title">Resources</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'documentation') active @endif">
          <a href="{{ url('docs/documentation') }}" class="nav-link"><i class="bi bi-journal-code"></i><span class="nav-label-text">Documentation</span></a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'auth-login') active @endif">
          <a href="{{ url('docs/auth-login') }}" class="nav-link"><i class="bi bi-box-arrow-in-right"></i><span class="nav-label-text">Auth Pages</span></a>
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
        <input type="text" placeholder="Cari menu, pengguna, atau dokumen...">
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
            <div class="user-avatar">RA</div>
            <div class="d-none d-md-block">
              <div class="user-chip-name">Rangga A.</div>
              <div class="user-chip-role">Administrator</div>
            </div>
            <i class="bi bi-chevron-down" style="font-size:0.7rem; color:var(--text-muted);"></i>
          </div>
          <ul class="dropdown-menu dropdown-menu-end mt-2">
            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
            <li><a class="dropdown-item" href="{{ url('docs/settings') }}"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ url('docs/auth-login') }}"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
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
<script src="{{ asset('nexora-assets/js/charts.js') }}"></script>
@stack('scripts')
</body>
</html>
