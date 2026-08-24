<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Menu') — {{ $outlet->outlet_name ?? 'Kasir POS' }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('guest/css/guest.css') }}">
@stack('styles')
</head>
<body>

<!-- Navbar -->
<nav class="guest-navbar">
  <div class="guest-navbar-inner">
    <a href="{{ route('guest.index', $table->table_id) }}" class="guest-brand">
      @if(!empty($outlet->outlet_image))
        <img src="{{ asset('storage/' . $outlet->outlet_image) }}" alt="" class="guest-brand-img">
      @else
        <span class="guest-brand-mark">{{ mb_substr($outlet->outlet_name ?? 'K', 0, 1) }}</span>
      @endif
      <span class="guest-brand-name">{{ $outlet->outlet_name ?? 'Kasir POS' }}</span>
    </a>
    <div class="guest-nav-actions">
      <span class="guest-table-chip"><i class="bi bi-grid-3x3-gap-fill"></i> Meja {{ $table->table_number }}</span>
    </div>
  </div>
</nav>

<main class="guest-container">
  @yield('content')
</main>

<!-- Offcanvas / Floating action -->
@yield('floating')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('guest/js/guest.js') }}"></script>
@stack('scripts')
</body>
</html>
