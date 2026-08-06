<!DOCTYPE html>
<html class="light" lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'Ignite & Spice') — {{ $company->company_name ?? 'Ignite & Spice' }}</title>

  <!-- Google Fonts: Montserrat & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <!-- Material Symbols Outlined -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

  <!-- Tailwind CSS CDN + Plugins -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-secondary": "#ffffff",
            "error-container": "#ffdad6",
            "surface-dim": "#ded9d8",
            "on-secondary-fixed": "#331200",
            "surface-tint": "#ba1a20",
            "primary": "#af101a",
            "outline-variant": "#e4beba",
            "surface-container-low": "#f8f2f1",
            "on-tertiary-container": "#f6f3f3",
            "secondary": "#9c4400",
            "on-tertiary-fixed-variant": "#474746",
            "primary-fixed-dim": "#ffb3ac",
            "inverse-primary": "#ffb3ac",
            "tertiary-fixed-dim": "#c8c6c5",
            "on-secondary-fixed-variant": "#773200",
            "tertiary-fixed": "#e5e2e1",
            "on-error-container": "#93000a",
            "on-error": "#ffffff",
            "on-primary-fixed": "#410003",
            "tertiary": "#575757",
            "on-primary-container": "#fff2f0",
            "secondary-container": "#fd7613",
            "background": "#fef8f7",
            "secondary-fixed-dim": "#ffb68f",
            "inverse-on-surface": "#f5efee",
            "on-tertiary-fixed": "#1b1c1c",
            "secondary-fixed": "#ffdbca",
            "on-primary": "#ffffff",
            "on-surface-variant": "#5b403d",
            "on-surface": "#1d1b1b",
            "surface": "#fef8f7",
            "inverse-surface": "#323030",
            "surface-bright": "#fef8f7",
            "on-background": "#1d1b1b",
            "tertiary-container": "#706f6f",
            "surface-container-high": "#ede7e6",
            "surface-container-lowest": "#ffffff",
            "surface-container-highest": "#e7e1e0",
            "outline": "#8f6f6c",
            "on-tertiary": "#ffffff",
            "error": "#ba1a1a",
            "primary-container": "#d32f2f",
            "on-secondary-container": "#5b2500",
            "surface-container": "#f2edec",
            "surface-variant": "#e7e1e0",
            "primary-fixed": "#ffdad6",
            "on-primary-fixed-variant": "#930010"
          },
          fontFamily: {
            "headline": ["Montserrat", "sans-serif"],
            "body": ["Inter", "sans-serif"]
          }
        }
      }
    }
  </script>

  <!-- Ignite & Spice Custom CSS -->
  <link rel="stylesheet" href="{{ asset('guest/ignite_spice/css/ignite_spice.css') }}"/>
  @stack('styles')
</head>
<body class="bg-background text-on-background font-body min-h-screen pb-24">

  <!-- Top Navigation Bar -->
  <header class="sticky top-0 z-50 bg-background/95 backdrop-blur border-b border-surface-variant shadow-xs w-full">
    <div class="flex justify-between items-center px-4 h-16 w-full max-w-7xl mx-auto">
      <div class="flex items-center gap-3">
        <a href="{{ route('guest.index', $table->table_id) }}" class="flex items-center gap-2.5 group">
          @if(!empty($company->company_image))
            <img src="{{ asset('storage/' . $company->company_image) }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover border border-primary/30 group-hover:scale-105 transition-transform"/>
          @else
            <div class="w-9 h-9 rounded-xl heat-gradient text-white flex items-center justify-center font-black shadow-md pulse-heat">
              <span class="material-symbols-outlined text-[20px] fill-icon">local_fire_department</span>
            </div>
          @endif
          <h1 class="text-xl font-headline font-black text-primary tracking-tight">
            {{ $company->company_name ?? 'Ignite & Spice' }}
          </h1>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5 px-3 py-1 bg-surface-container-low border border-outline-variant/60 rounded-full text-primary font-headline font-bold text-xs shadow-xs">
          <span class="w-2 h-2 rounded-full bg-secondary-container animate-pulse"></span>
          MEJA {{ $table->table_number }}
        </div>
        <a href="{{ route('guest.status', $table->table_id) }}" class="p-2 text-primary hover:bg-surface-container-high rounded-full transition-colors relative" title="Status Pesanan">
          <span class="material-symbols-outlined">receipt_long</span>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 mt-6">
    @yield('content')
  </main>

  <!-- Bottom Floating / Modals Section -->
  @yield('floating')

  <!-- Bottom Navigation Bar -->
  <nav class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-3 pt-2 bg-surface shadow-[0_-4px_12px_rgba(0,0,0,0.06)] border-t border-surface-variant">
    <a href="{{ route('guest.index', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.index' ? 'heat-gradient text-white font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.index' ? 'fill-icon' : '' }}">restaurant_menu</span>
      <span class="text-xs font-semibold mt-0.5">Menu</span>
    </a>
    <a href="{{ route('guest.status', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.status' ? 'heat-gradient text-white font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.status' ? 'fill-icon' : '' }}">local_fire_department</span>
      <span class="text-xs font-semibold mt-0.5">Pesanan</span>
    </a>
  </nav>

  <!-- Ignite & Spice Script -->
  <script src="{{ asset('guest/ignite_spice/js/ignite_spice.js') }}"></script>
  
  @if(session('success'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        NexoraToast(@json(session('success')), 'success');
      });
    </script>
  @endif

  @if(session('error'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        NexoraToast(@json(session('error')), 'danger');
      });
    </script>
  @endif

  @stack('scripts')
</body>
</html>
