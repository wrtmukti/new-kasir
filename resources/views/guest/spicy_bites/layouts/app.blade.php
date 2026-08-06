<!DOCTYPE html>
<html class="light" lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>
  <title>@yield('title', 'Spicy Bites Menu') — {{ $company->company_name ?? 'Spicy Bites' }}</title>

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
            "on-background": "#1b1c1c",
            "inverse-primary": "#ffb3ac",
            "tertiary": "#005f7b",
            "secondary-container": "#fdc003",
            "tertiary-fixed": "#bee9ff",
            "background": "#fcf9f8",
            "primary-fixed-dim": "#ffb3ac",
            "surface-container-high": "#eae7e7",
            "surface-container-lowest": "#ffffff",
            "on-surface": "#1b1c1c",
            "surface-variant": "#e5e2e1",
            "surface-container-highest": "#e5e2e1",
            "on-tertiary-container": "#e9f7ff",
            "on-secondary-container": "#6c5000",
            "surface-bright": "#fcf9f8",
            "on-primary-container": "#fff2f0",
            "error": "#ba1a1a",
            "on-primary": "#ffffff",
            "surface-dim": "#dcd9d9",
            "on-error-container": "#93000a",
            "outline": "#8f6f6c",
            "on-secondary-fixed": "#261a00",
            "surface": "#fcf9f8",
            "on-surface-variant": "#5b403d",
            "secondary": "#785900",
            "on-error": "#ffffff",
            "surface-tint": "#ba1a20",
            "tertiary-container": "#00799c",
            "on-tertiary": "#ffffff",
            "primary": "#af101a",
            "on-primary-fixed": "#410003",
            "primary-fixed": "#ffdad6",
            "on-secondary-fixed-variant": "#5b4300",
            "on-secondary": "#ffffff",
            "tertiary-fixed-dim": "#7bd1f8",
            "inverse-surface": "#303030",
            "inverse-on-surface": "#f3f0ef",
            "on-tertiary-fixed-variant": "#004d65",
            "on-primary-fixed-variant": "#930010",
            "surface-container": "#f0eded",
            "secondary-fixed-dim": "#fabd00",
            "error-container": "#ffdad6",
            "secondary-fixed": "#ffdf9e",
            "on-tertiary-fixed": "#001f2a",
            "outline-variant": "#e4beba",
            "surface-container-low": "#f6f3f2",
            "primary-container": "#d32f2f"
          },
          fontFamily: {
            "headline": ["Montserrat", "sans-serif"],
            "body": ["Inter", "sans-serif"]
          }
        }
      }
    }
  </script>

  <!-- Spicy Bites Custom CSS -->
  <link rel="stylesheet" href="{{ asset('guest/spicy_bites/css/spicy_bites.css') }}"/>
  @stack('styles')
</head>
<body class="bg-background text-on-background font-body min-h-screen pb-24">

  <!-- Top Navigation Bar -->
  <header class="sticky top-0 z-50 bg-background/95 backdrop-blur border-b border-surface-variant shadow-sm w-full">
    <div class="flex justify-between items-center px-4 h-16 w-full max-w-7xl mx-auto">
      <div class="flex items-center gap-3">
        <a href="{{ route('guest.index', $table->table_id) }}" class="flex items-center gap-2 group">
          @if(!empty($company->company_image))
            <img src="{{ asset('storage/' . $company->company_image) }}" alt="Logo" class="w-9 h-9 rounded-full object-cover border border-primary/30 group-hover:scale-105 transition-transform"/>
          @else
            <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-black shadow-sm pulse-flame">
              <span class="material-symbols-outlined text-[20px] fill-icon">local_fire_department</span>
            </div>
          @endif
          <h1 class="text-xl font-headline font-black text-primary uppercase tracking-tighter">
            {{ $company->company_name ?? 'Spicy Bites' }}
          </h1>
        </a>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center gap-1.5 px-3 py-1 bg-secondary-container rounded-full text-on-secondary-container font-headline font-bold text-xs shadow-sm">
          <span class="w-2 h-2 rounded-full bg-green-600 animate-pulse"></span>
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
  <nav class="fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-3 pt-2 bg-surface shadow-[0_-4px_12px_rgba(0,0,0,0.08)] border-t border-surface-variant">
    <a href="{{ route('guest.index', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.index' ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.index' ? 'fill-icon' : '' }}">restaurant_menu</span>
      <span class="text-xs font-semibold mt-0.5">Menu</span>
    </a>
    <a href="{{ route('guest.status', $table->table_id) }}" class="flex flex-col items-center justify-center {{ Route::currentRouteName() === 'guest.status' ? 'bg-primary-container text-on-primary-container font-bold' : 'text-on-surface-variant hover:text-primary' }} rounded-xl px-4 py-1.5 transition-all active:scale-95 duration-150">
      <span class="material-symbols-outlined text-[22px] {{ Route::currentRouteName() === 'guest.status' ? 'fill-icon' : '' }}">local_fire_department</span>
      <span class="text-xs font-semibold mt-0.5">Pesanan</span>
    </a>
  </nav>

  <!-- Spicy Bites Script -->
  <script src="{{ asset('guest/spicy_bites/js/spicy_bites.js') }}"></script>
  
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
