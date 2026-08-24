<!DOCTYPE html>
<html class="light" lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'Metropolis Brew') — {{ $outlet->outlet_name ?? 'Metropolis Brew' }}</title>

  <!-- Google Fonts: Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
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
            "surface-bright": "#f9f9ff",
            "on-error": "#ffffff",
            "inverse-primary": "#68dba9",
            "on-tertiary-fixed": "#191c1d",
            "primary-fixed-dim": "#68dba9",
            "error-container": "#ffdad6",
            "surface-container-low": "#f0f3ff",
            "on-background": "#151c27",
            "on-secondary": "#ffffff",
            "tertiary-container": "#737576",
            "on-primary-container": "#f5fff7",
            "on-primary-fixed": "#002114",
            "surface-container": "#e7eefe",
            "tertiary-fixed": "#e1e3e4",
            "surface-dim": "#d3daea",
            "surface-container-highest": "#dce2f3",
            "primary-container": "#00855d",
            "outline-variant": "#bccac0",
            "secondary": "#575e70",
            "on-tertiary-fixed-variant": "#454748",
            "inverse-surface": "#2a313d",
            "surface-tint": "#006c4a",
            "on-error-container": "#93000a",
            "primary-fixed": "#85f8c4",
            "outline": "#6d7a72",
            "secondary-fixed-dim": "#c0c6db",
            "tertiary": "#5a5c5d",
            "on-tertiary-container": "#fcfdfe",
            "on-surface-variant": "#3d4a42",
            "on-primary": "#ffffff",
            "on-tertiary": "#ffffff",
            "primary": "#006948",
            "surface": "#f9f9ff",
            "on-secondary-fixed-variant": "#404758",
            "surface-variant": "#dce2f3",
            "secondary-fixed": "#dce2f7",
            "error": "#ba1a1a",
            "inverse-on-surface": "#ebf1ff",
            "surface-container-high": "#e2e8f8",
            "surface-container-lowest": "#ffffff",
            "background": "#f9f9ff",
            "tertiary-fixed-dim": "#c5c7c8",
            "on-secondary-container": "#5c6274",
            "on-primary-fixed-variant": "#005137",
            "secondary-container": "#d9dff5",
            "on-surface": "#151c27",
            "on-secondary-fixed": "#141b2b"
          },
          fontFamily: {
            "headline": ["Manrope", "sans-serif"],
            "body": ["Manrope", "sans-serif"]
          }
        }
      }
    }
  </script>

  <!-- Metropolis Brew Custom CSS -->
  <link rel="stylesheet" href="{{ asset('guest/metropolis_brew/css/metropolis_brew.css') }}"/>
  @stack('styles')
</head>
<body class="bg-background text-on-background font-body min-h-screen pb-24">

  <!-- Top Navigation Bar -->
  <header class="sticky top-0 z-50 bg-background/95 backdrop-blur border-b border-surface-variant shadow-xs w-full">
    <div class="flex justify-between items-center px-4 h-16 w-full max-w-7xl mx-auto">
      <div class="flex items-center gap-3">
        <a href="{{ route('guest.index', $table->table_id) }}" class="flex items-center gap-2.5 group">
          @if(!empty($outlet->outlet_image))
            <img src="{{ asset('storage/' . $outlet->outlet_image) }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover border border-primary/30 group-hover:scale-105 transition-transform"/>
          @else
            <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-black shadow-sm">
              <span class="material-symbols-outlined text-[20px] fill-icon">local_cafe</span>
            </div>
          @endif
          <h1 class="text-xl font-headline font-extrabold text-primary tracking-tight">
            {{ $outlet->outlet_name ?? 'Metropolis Brew' }}
          </h1>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5 px-3 py-1 bg-surface-container-low border border-outline-variant/60 rounded-full text-primary font-headline font-bold text-xs shadow-xs">
          <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
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
    <a href="{{ route('guest.index', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.index' ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.index' ? 'fill-icon' : '' }}">restaurant_menu</span>
      <span class="text-xs font-semibold mt-0.5">Menu</span>
    </a>
    <a href="{{ route('guest.status', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.status' ? 'bg-primary-container text-white font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.status' ? 'fill-icon' : '' }}">coffee</span>
      <span class="text-xs font-semibold mt-0.5">Pesanan</span>
    </a>
  </nav>

  <!-- Metropolis Brew Script -->
  <script src="{{ asset('guest/metropolis_brew/js/metropolis_brew.js') }}"></script>
  
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
