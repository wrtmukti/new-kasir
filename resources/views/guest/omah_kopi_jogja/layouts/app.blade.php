<!DOCTYPE html>
<html class="light" lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'Omah Kopi Jogja') — {{ $outlet->outlet_name ?? 'Omah Kopi Jogja' }}</title>

  <!-- Google Fonts: Playfair Display & Manrope -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet"/>
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
            "on-secondary-fixed-variant": "#59422c",
            "on-tertiary-fixed-variant": "#293ca0",
            "secondary-fixed-dim": "#e1c1a4",
            "secondary-fixed": "#fedcbe",
            "surface-container-low": "#f7f3ee",
            "on-tertiary-fixed": "#00105c",
            "surface-container-high": "#ebe8e3",
            "tertiary-container": "#5668cd",
            "tertiary-fixed": "#dee0ff",
            "surface-tint": "#9c432d",
            "on-error": "#ffffff",
            "background": "#fdf9f4",
            "surface-container": "#f1ede8",
            "primary-fixed": "#ffdad2",
            "on-primary-container": "#fff3f0",
            "on-secondary": "#ffffff",
            "on-secondary-container": "#796048",
            "outline": "#89726d",
            "inverse-primary": "#ffb4a3",
            "tertiary": "#3c4fb2",
            "on-error-container": "#93000a",
            "on-primary-fixed-variant": "#7d2c18",
            "surface-container-highest": "#e6e2dd",
            "on-surface-variant": "#56423e",
            "primary": "#943d28",
            "on-primary": "#ffffff",
            "on-surface": "#1c1c19",
            "inverse-on-surface": "#f4f0eb",
            "on-secondary-fixed": "#291806",
            "on-tertiary": "#ffffff",
            "inverse-surface": "#31302d",
            "surface-dim": "#ddd9d5",
            "on-primary-fixed": "#3d0700",
            "error-container": "#ffdad6",
            "secondary-container": "#fedcbe",
            "error": "#ba1a1a",
            "surface": "#fdf9f4",
            "surface-container-lowest": "#ffffff",
            "on-background": "#1c1c19",
            "primary-container": "#b3543d",
            "primary-fixed-dim": "#ffb4a3",
            "surface-variant": "#e6e2dd",
            "on-tertiary-container": "#f6f4ff",
            "surface-bright": "#fdf9f4",
            "outline-variant": "#dcc1ba",
            "tertiary-fixed-dim": "#bac3ff",
            "secondary": "#725a42"
          },
          fontFamily: {
            "headline": ["Playfair Display", "serif"],
            "body": ["Manrope", "sans-serif"]
          }
        }
      }
    }
  </script>

  <!-- Omah Kopi Jogja Custom CSS -->
  <link rel="stylesheet" href="{{ asset('guest/omah_kopi_jogja/css/omah_kopi_jogja.css') }}"/>
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
            <div class="w-9 h-9 rounded-xl bg-primary text-white flex items-center justify-center font-black shadow-md">
              <span class="material-symbols-outlined text-[20px] fill-icon">coffee</span>
            </div>
          @endif
          <h1 class="text-xl font-headline font-black text-primary tracking-tight">
            {{ $outlet->outlet_name ?? 'Omah Kopi Jogja' }}
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
    <a href="{{ route('guest.index', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.index' ? 'bg-primary text-white font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.index' ? 'fill-icon' : '' }}">restaurant_menu</span>
      <span class="text-xs font-semibold mt-0.5">Menu</span>
    </a>
    <a href="{{ route('guest.status', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.status' ? 'bg-primary text-white font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.status' ? 'fill-icon' : '' }}">coffee</span>
      <span class="text-xs font-semibold mt-0.5">Pesanan</span>
    </a>
  </nav>

  <!-- Omah Kopi Jogja Script -->
  <script src="{{ asset('guest/omah_kopi_jogja/js/omah_kopi_jogja.js') }}"></script>
  
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
