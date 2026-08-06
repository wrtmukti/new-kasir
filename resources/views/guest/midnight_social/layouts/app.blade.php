<!DOCTYPE html>
<html class="dark" lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'Midnight Social') — {{ $company->company_name ?? 'Midnight Social' }}</title>

  <!-- Google Fonts: Sora & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
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
            "surface": "#0c1324",
            "on-tertiary-fixed": "#100563",
            "surface-container-lowest": "#070d1f",
            "surface-tint": "#d2bbff",
            "on-secondary": "#00344d",
            "on-surface": "#dce1fb",
            "on-primary-container": "#ede0ff",
            "primary-fixed": "#eaddff",
            "background": "#0c1324",
            "on-surface-variant": "#ccc3d8",
            "surface-container": "#191f31",
            "secondary-fixed-dim": "#89ceff",
            "error-container": "#93000a",
            "surface-dim": "#0c1324",
            "on-secondary-container": "#00344e",
            "tertiary-fixed-dim": "#c3c0ff",
            "on-secondary-fixed": "#001e2f",
            "tertiary-container": "#5f5db1",
            "on-tertiary": "#272377",
            "surface-bright": "#33394c",
            "primary-container": "#7c3aed",
            "surface-container-low": "#151b2d",
            "on-primary": "#3f008e",
            "outline-variant": "#4a4455",
            "on-primary-fixed": "#25005a",
            "on-background": "#dce1fb",
            "secondary": "#89ceff",
            "tertiary-fixed": "#e2dfff",
            "on-error": "#690005",
            "primary-fixed-dim": "#d2bbff",
            "secondary-container": "#00a2e6",
            "inverse-primary": "#732ee4",
            "surface-container-high": "#23293c",
            "on-secondary-fixed-variant": "#004c6e",
            "inverse-surface": "#dce1fb",
            "on-primary-fixed-variant": "#5a00c6",
            "error": "#ffb4ab",
            "primary": "#d2bbff",
            "on-error-container": "#ffdad6",
            "on-tertiary-fixed-variant": "#3e3c8f",
            "inverse-on-surface": "#2a3043",
            "outline": "#958da1",
            "on-tertiary-container": "#e6e3ff",
            "surface-variant": "#2e3447",
            "surface-container-highest": "#2e3447",
            "secondary-fixed": "#c9e6ff",
            "tertiary": "#c3c0ff"
          },
          fontFamily: {
            "headline": ["Sora", "sans-serif"],
            "body": ["Inter", "sans-serif"]
          }
        }
      }
    }
  </script>

  <!-- Midnight Social Custom CSS -->
  <link rel="stylesheet" href="{{ asset('guest/midnight_social/css/midnight_social.css') }}"/>
  @stack('styles')
</head>
<body class="bg-[#020617] text-[#dce1fb] font-body min-h-screen pb-24">

  <!-- Top Navigation Bar -->
  <header class="sticky top-0 z-50 bg-[#0f172a]/80 backdrop-blur border-b border-white/10 shadow-lg w-full">
    <div class="flex justify-between items-center px-4 h-16 w-full max-w-7xl mx-auto">
      <div class="flex items-center gap-3">
        <a href="{{ route('guest.index', $table->table_id) }}" class="flex items-center gap-2.5 group">
          @if(!empty($company->company_image))
            <img src="{{ asset('storage/' . $company->company_image) }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover border border-purple-500/40 group-hover:scale-105 transition-transform"/>
          @else
            <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center font-black shadow-md">
              <span class="material-symbols-outlined text-[20px] fill-icon">nightlife</span>
            </div>
          @endif
          <h1 class="text-xl font-headline font-extrabold text-purple-300 tracking-tight">
            {{ $company->company_name ?? 'Midnight Social' }}
          </h1>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5 px-3 py-1 bg-purple-950/60 border border-purple-500/30 rounded-full text-purple-300 font-headline font-bold text-xs shadow-xs">
          <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
          MEJA {{ $table->table_number }}
        </div>
        <a href="{{ route('guest.status', $table->table_id) }}" class="p-2 text-purple-300 hover:bg-white/10 rounded-full transition-colors relative" title="Status Pesanan">
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
  <nav class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-3 pt-2 bg-[#0f172a]/90 backdrop-blur shadow-[0_-4px_20px_rgba(0,0,0,0.5)] border-t border-white/10">
    <a href="{{ route('guest.index', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.index' ? 'bg-purple-600 text-white font-bold' : 'text-slate-400 hover:text-purple-300' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.index' ? 'fill-icon' : '' }}">restaurant_menu</span>
      <span class="text-xs font-semibold mt-0.5">Menu</span>
    </a>
    <a href="{{ route('guest.status', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.status' ? 'bg-purple-600 text-white font-bold' : 'text-slate-400 hover:text-purple-300' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.status' ? 'fill-icon' : '' }}">nightlife</span>
      <span class="text-xs font-semibold mt-0.5">Pesanan</span>
    </a>
  </nav>

  <!-- Midnight Social Script -->
  <script src="{{ asset('guest/midnight_social/js/midnight_social.js') }}"></script>
  
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
