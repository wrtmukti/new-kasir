<!DOCTYPE html>
<html class="light" lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'Bumblebee Cafe') — {{ $outlet->outlet_name ?? 'Bumblebee Cafe' }}</title>

  <!-- Google Fonts: Montserrat & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
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
            "on-secondary-fixed-variant": "#474747",
            "on-background": "#1a1c1c",
            "surface-container-highest": "#e2e2e2",
            "on-error": "#ffffff",
            "on-primary-fixed-variant": "#544600",
            "inverse-on-surface": "#f0f1f1",
            "on-secondary-container": "#656464",
            "primary": "#705d00",
            "tertiary-fixed-dim": "#00dbe8",
            "surface-variant": "#e2e2e2",
            "primary-fixed-dim": "#e9c400",
            "error-container": "#ffdad6",
            "inverse-primary": "#e9c400",
            "secondary": "#5f5e5e",
            "on-primary-fixed": "#221b00",
            "surface-container": "#eeeeee",
            "surface-dim": "#dadada",
            "on-tertiary-container": "#006a70",
            "on-surface": "#1a1c1c",
            "surface": "#f9f9f9",
            "secondary-container": "#e4e2e1",
            "on-secondary-fixed": "#1b1c1c",
            "secondary-fixed-dim": "#c8c6c6",
            "background": "#f9f9f9",
            "surface-container-low": "#f3f3f4",
            "on-tertiary-fixed": "#002022",
            "surface-tint": "#705d00",
            "primary-fixed": "#ffe16d",
            "inverse-surface": "#2f3131",
            "secondary-fixed": "#e4e2e1",
            "surface-container-high": "#e8e8e8",
            "on-primary-container": "#705e00",
            "tertiary": "#00696f",
            "on-secondary": "#ffffff",
            "outline-variant": "#d0c6ab",
            "surface-bright": "#f9f9f9",
            "surface-container-lowest": "#ffffff",
            "on-error-container": "#93000a",
            "error": "#ba1a1a",
            "tertiary-fixed": "#79f5ff",
            "on-surface-variant": "#4d4732",
            "on-primary": "#ffffff",
            "outline": "#7e775f",
            "on-tertiary-fixed-variant": "#004f54",
            "primary-container": "#ffd700",
            "tertiary-container": "#00f1ff",
            "on-tertiary": "#ffffff"
          },
          fontFamily: {
            "headline": ["Montserrat", "sans-serif"],
            "body": ["Inter", "sans-serif"]
          }
        }
      }
    }
  </script>

  <!-- Bumblebee Cafe Custom CSS -->
  <link rel="stylesheet" href="{{ asset('guest/bumblebee/css/bumblebee.css') }}"/>
  @stack('styles')
</head>
<body class="bg-background text-on-background font-body min-h-screen pb-24">

  <!-- Top Navigation Bar -->
  <header class="sticky top-0 z-50 bg-background/95 backdrop-blur border-b border-surface-variant shadow-xs w-full">
    <div class="flex justify-between items-center px-4 h-16 w-full max-w-7xl mx-auto">
      <div class="flex items-center gap-3">
        <a href="{{ route('guest.index', $table->table_id) }}" class="flex items-center gap-2.5 group">
          @if(!empty($outlet->outlet_image))
            <img src="{{ asset('storage/' . $outlet->outlet_image) }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover border border-amber-300 group-hover:scale-105 transition-transform"/>
          @else
            <div class="w-9 h-9 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center font-black shadow-md">
              <span class="material-symbols-outlined text-[20px] fill-icon">emoji_nature</span>
            </div>
          @endif
          <h1 class="text-xl font-headline font-black text-amber-800 tracking-tight">
            {{ $outlet->outlet_name ?? 'Bumblebee Cafe' }}
          </h1>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5 px-3 py-1 bg-amber-100 border border-amber-300 rounded-full text-amber-900 font-headline font-bold text-xs shadow-xs">
          <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
          MEJA {{ $table->table_number }}
        </div>
        <a href="{{ route('guest.status', $table->table_id) }}" class="p-2 text-amber-800 hover:bg-amber-100 rounded-full transition-colors relative" title="Status Pesanan">
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
    <a href="{{ route('guest.index', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.index' ? 'bg-amber-400 text-slate-900 font-bold shadow-xs' : 'text-on-surface-variant hover:text-amber-800' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.index' ? 'fill-icon' : '' }}">restaurant_menu</span>
      <span class="text-xs font-semibold mt-0.5">Menu</span>
    </a>
    <a href="{{ route('guest.status', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.status' ? 'bg-amber-400 text-slate-900 font-bold shadow-xs' : 'text-on-surface-variant hover:text-amber-800' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.status' ? 'fill-icon' : '' }}">emoji_nature</span>
      <span class="text-xs font-semibold mt-0.5">Pesanan</span>
    </a>
  </nav>

  <!-- Bumblebee Cafe Script -->
  <script src="{{ asset('guest/bumblebee/js/bumblebee.js') }}"></script>
  
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
