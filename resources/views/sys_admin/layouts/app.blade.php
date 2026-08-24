<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Control Panel') — Nexora System Admin</title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('nexora-assets/css/main.css') }}">
  <script>
    (function() {
      const saved = localStorage.getItem('nexora-theme') || 'dark';
      document.documentElement.setAttribute('data-theme', saved);
    })();
  </script>
  @stack('styles')
</head>
<body>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="app-shell">

  <!-- ============ SYSTEM ADMIN SIDEBAR ============ -->
  <aside class="sidebar" id="appSidebar">
    <div class="sidebar-brand d-flex align-items-center gap-2">
      <div class="brand-mark rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width:36px; height:36px; background: linear-gradient(135deg, #3b82f6, #6366f1); font-size:1.1rem;">
        <i class="bi bi-shield-lock-fill"></i>
      </div>
      <div>
        <span class="brand-name fw-bold" style="font-size:1.05rem;">Nexora</span>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill d-block text-start px-2 py-0.5 mt-0.5" style="font-size:0.6rem; width:max-content;">
          SYSTEM CONTROL
        </span>
      </div>
    </div>

    <nav class="sidebar-nav scroll-thin">
      {{-- DASHBOARD --}}
      <div class="nav-section-title">Dashboard</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'dashboard') active @endif">
          <a href="{{ route('sys_admin.dashboard') }}" class="nav-link">
            <i class="bi bi-speedometer2 text-primary"></i>
            <span class="nav-label-text">Overview Dashboard</span>
          </a>
        </li>
      </ul>

      {{-- TENANT MANAGEMENT --}}
      <div class="nav-section-title">Client Management</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'clients') active @endif">
          <a href="{{ Route::has('sys_admin.clients.index') ? route('sys_admin.clients.index') : '#' }}" class="nav-link">
            <i class="bi bi-buildings-fill text-info"></i>
            <span class="nav-label-text">Clients</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'outlets') active @endif">
          <a href="{{ Route::has('sys_admin.outlets.index') ? route('sys_admin.outlets.index') : '#' }}" class="nav-link">
            <i class="bi bi-shop-window text-success"></i>
            <span class="nav-label-text">Outlets (Cabang)</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'users') active @endif">
          <a href="{{ Route::has('sys_admin.users.index') ? route('sys_admin.users.index') : '#' }}" class="nav-link">
            <i class="bi bi-people-fill text-warning"></i>
            <span class="nav-label-text">Users Overview</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'plans') active @endif">
          <a href="{{ Route::has('sys_admin.plans.index') ? route('sys_admin.plans.index') : '#' }}" class="nav-link">
            <i class="bi bi-box-seam-fill text-danger"></i>
            <span class="nav-label-text">Plans & Fitur</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'subscriptions') active @endif">
          <a href="{{ Route::has('sys_admin.subscriptions.index') ? route('sys_admin.subscriptions.index') : '#' }}" class="nav-link">
            <i class="bi bi-credit-card-2-front-fill text-primary"></i>
            <span class="nav-label-text">Subscriptions</span>
          </a>
        </li>
      </ul>

      {{-- INFRASTRUCTURE --}}
      <div class="nav-section-title">Infrastructure</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'databases') active @endif">
          <a href="{{ Route::has('sys_admin.databases.index') ? route('sys_admin.databases.index') : '#' }}" class="nav-link">
            <i class="bi bi-database-fill-gear text-info"></i>
            <span class="nav-label-text">Client Databases</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'backups') active @endif">
          <a href="{{ Route::has('sys_admin.backups.index') ? route('sys_admin.backups.index') : '#' }}" class="nav-link">
            <i class="bi bi-cloud-arrow-down-fill text-success"></i>
            <span class="nav-label-text">Backup Management</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'health') active @endif">
          <a href="{{ Route::has('sys_admin.health.index') ? route('sys_admin.health.index') : '#' }}" class="nav-link">
            <i class="bi bi-activity text-danger"></i>
            <span class="nav-label-text">System Health</span>
          </a>
        </li>
      </ul>

      {{-- SECURITY --}}
      <div class="nav-section-title">Security & Logs</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'audit_logs') active @endif">
          <a href="{{ Route::has('sys_admin.audit_logs.index') ? route('sys_admin.audit_logs.index') : '#' }}" class="nav-link">
            <i class="bi bi-shield-check text-warning"></i>
            <span class="nav-label-text">Audit Logs</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'notifications') active @endif">
          <a href="{{ Route::has('sys_admin.notifications.index') ? route('sys_admin.notifications.index') : '#' }}" class="nav-link">
            <i class="bi bi-bell-fill text-secondary"></i>
            <span class="nav-label-text">System Alerts</span>
          </a>
        </li>
      </ul>

      {{-- SYSTEM --}}
      <div class="nav-section-title">System Configuration</div>
      <ul class="list-unstyled">
        <li class="nav-item @if(($activeMenu ?? '') === 'settings') active @endif">
          <a href="{{ Route::has('sys_admin.settings.index') ? route('sys_admin.settings.index') : '#' }}" class="nav-link">
            <i class="bi bi-gear-fill text-primary"></i>
            <span class="nav-label-text">Global Settings</span>
          </a>
        </li>
        <li class="nav-item @if(($activeMenu ?? '') === 'tools') active @endif">
          <a href="{{ Route::has('sys_admin.tools.index') ? route('sys_admin.tools.index') : '#' }}" class="nav-link">
            <i class="bi bi-tools text-info"></i>
            <span class="nav-label-text">System Tools</span>
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
        <input type="text" placeholder="Cari client, subscription, database...">
        <span class="kbd-hint">⌘K</span>
      </div>

      <div class="topbar-actions">
        {{-- Theme Switcher --}}
        <button class="icon-btn" id="themeToggleBtn" aria-label="Ganti tema">
          <i class="bi bi-sun"></i>
        </button>

        {{-- Notifications Bell --}}
        <button class="icon-btn" aria-label="Notifikasi">
          <i class="bi bi-bell-fill"></i>
          <span class="dot-badge"></span>
        </button>

        {{-- User Chip Profile --}}
        @php
          $currentUser = auth('system_admin')->user();
        @endphp
        <div class="dropdown">
          <div class="user-chip" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
            <div class="user-avatar rounded-circle fw-bold text-white d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6);">
              {{ strtoupper(substr($currentUser->name ?? 'SA', 0, 2)) }}
            </div>
            <div class="d-none d-md-block text-start">
              <div class="user-chip-name fw-semibold" style="font-size:0.85rem;">{{ $currentUser->name ?? 'Super Admin' }}</div>
              <div class="user-chip-role text-muted-c text-uppercase" style="font-size:0.68rem;">{{ str_replace('_', ' ', $currentUser->role ?? 'super_admin') }}</div>
            </div>
            <i class="bi bi-chevron-down" style="font-size:0.7rem; color:var(--text-muted);"></i>
          </div>
          <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-lg border-0" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; border-radius: 0.75rem;">
            <li class="px-3 py-2 border-bottom" style="border-color: var(--border-subtle) !important;">
              <small class="text-muted-c d-block" style="font-size:0.72rem;">Login sebagai:</small>
              <div class="fw-bold" style="font-size:0.85rem; color:var(--text-primary);">{{ $currentUser->email ?? 'admin@system.local' }}</div>
            </li>
            <li>
              <a class="dropdown-item py-2" href="#"><i class="bi bi-person me-2 text-primary"></i>Profil Akun</a>
            </li>
            <li>
              <a class="dropdown-item py-2" href="#"><i class="bi bi-shield-lock me-2 text-info"></i>Keamanan</a>
            </li>
            <li><hr class="dropdown-divider my-1" style="border-color: var(--border-subtle);"></li>
            <li>
              <form action="{{ route('sys_admin.logout') }}" method="POST" id="formSysAdminLogout">
                @csrf
                <button type="submit" class="dropdown-item py-2 text-danger">
                  <i class="bi bi-box-arrow-right me-2"></i>Keluar Sistem
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
