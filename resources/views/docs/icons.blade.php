@extends('docs.layouts.app')

@section('title', 'Icons')

@php $activeMenu = 'icons' @endphp

@push('styles')
<style>
  .icon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 0.75rem;
  }
  .icon-grid .icon-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    padding: 1rem 0.4rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-subtle);
    background: var(--bg-surface);
    transition: all var(--transition);
    cursor: default;
  }
  .icon-grid .icon-item:hover {
    border-color: var(--accent-1);
    background: var(--bg-elevated);
    transform: translateY(-2px);
  }
  .icon-grid .icon-item i { font-size: 1.6rem; color: var(--text-secondary); }
  .icon-grid .icon-item span { font-size: 0.65rem; color: var(--text-muted); text-align: center; word-break: break-all; max-width: 100%; }
  .icon-grid .icon-item:hover i { color: var(--accent-1); }

  .icon-swatch {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px; height: 48px;
    border-radius: var(--radius-sm);
    font-size: 1.3rem;
    flex-shrink: 0;
  }
</style>
@endpush

@section('content')
      <div class="page-header">
        <div>
          <h1>Icons</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><a href="{{ url('docs/components') }}">UI Components</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Icons</span></div>
        </div>
        <p class="text-muted-c mb-0">Menggunakan <a href="https://icons.getbootstrap.com/" target="_blank" style="color:var(--accent-cyan);">Bootstrap Icons</a> v1.11.3 — 2,000+ ikon vector, mudah digunakan via class <code class="text-mono">bi bi-*</code>.</p>
      </div>

      <!-- Ukuran & Warna -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Ukuran &amp; Warna</h6></div>
        <div class="card-body">
          <div class="d-flex flex-wrap gap-3 align-items-center">
            <div class="icon-swatch" style="font-size:0.8rem;background:var(--bg-elevated-2);"><i class="bi bi-star-fill"></i></div>
            <div class="icon-swatch" style="font-size:1rem;background:var(--bg-elevated-2);"><i class="bi bi-star-fill"></i></div>
            <div class="icon-swatch" style="font-size:1.3rem;background:var(--bg-elevated-2);"><i class="bi bi-star-fill"></i></div>
            <div class="icon-swatch" style="font-size:1.8rem;background:var(--bg-elevated-2);"><i class="bi bi-star-fill"></i></div>
            <div class="icon-swatch" style="font-size:2.4rem;background:var(--bg-elevated-2);"><i class="bi bi-star-fill"></i></div>
          </div>
          <hr class="divider-line">
          <div class="d-flex flex-wrap gap-2">
            <i class="bi bi-heart-fill" style="color:var(--danger);font-size:1.3rem;"></i>
            <i class="bi bi-check-circle-fill" style="color:var(--success);font-size:1.3rem;"></i>
            <i class="bi bi-info-circle-fill" style="color:var(--info);font-size:1.3rem;"></i>
            <i class="bi bi-exclamation-triangle-fill" style="color:var(--warning);font-size:1.3rem;"></i>
            <i class="bi bi-gear-fill" style="color:var(--accent-1);font-size:1.3rem;"></i>
            <i class="bi bi-star-fill" style="color:var(--text-muted);font-size:1.3rem;"></i>
          </div>
        </div>
      </div>

      <!-- Navigation & UI -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Navigasi &amp; UI</h6></div>
        <div class="card-body">
          <div class="icon-grid">
            <div class="icon-item"><i class="bi bi-grid-1x2-fill"></i><span>bi-grid-1x2</span></div>
            <div class="icon-item"><i class="bi bi-grid-3x3-gap-fill"></i><span>bi-grid-3x3</span></div>
            <div class="icon-item"><i class="bi bi-layout-sidebar"></i><span>bi-layout</span></div>
            <div class="icon-item"><i class="bi bi-list"></i><span>bi-list</span></div>
            <div class="icon-item"><i class="bi bi-search"></i><span>bi-search</span></div>
            <div class="icon-item"><i class="bi bi-bell-fill"></i><span>bi-bell</span></div>
            <div class="icon-item"><i class="bi bi-gear-fill"></i><span>bi-gear</span></div>
            <div class="icon-item"><i class="bi bi-sliders"></i><span>bi-sliders</span></div>
            <div class="icon-item"><i class="bi bi-chevron-down"></i><span>bi-chevron</span></div>
            <div class="icon-item"><i class="bi bi-arrow-right"></i><span>bi-arrow</span></div>
            <div class="icon-item"><i class="bi bi-x-circle"></i><span>bi-x-circle</span></div>
            <div class="icon-item"><i class="bi bi-plus-lg"></i><span>bi-plus-lg</span></div>
            <div class="icon-item"><i class="bi bi-download"></i><span>bi-download</span></div>
            <div class="icon-item"><i class="bi bi-upload"></i><span>bi-upload</span></div>
            <div class="icon-item"><i class="bi bi-share-fill"></i><span>bi-share</span></div>
            <div class="icon-item"><i class="bi bi-bookmark-fill"></i><span>bi-bookmark</span></div>
            <div class="icon-item"><i class="bi bi-bookmark-star"></i><span>bi-bookmark*</span></div>
            <div class="icon-item"><i class="bi bi-pin-map-fill"></i><span>bi-pin-map</span></div>
            <div class="icon-item"><i class="bi bi-house-door-fill"></i><span>bi-house</span></div>
            <div class="icon-item"><i class="bi bi-folder-fill"></i><span>bi-folder</span></div>
            <div class="icon-item"><i class="bi bi-file-earmark"></i><span>bi-file</span></div>
            <div class="icon-item"><i class="bi bi-clipboard-data"></i><span>bi-clipboard</span></div>
            <div class="icon-item"><i class="bi bi-calendar-event"></i><span>bi-calendar</span></div>
            <div class="icon-item"><i class="bi bi-clock"></i><span>bi-clock</span></div>
          </div>
        </div>
      </div>

      <!-- People & Communication -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>People &amp; Communication</h6></div>
        <div class="card-body">
          <div class="icon-grid">
            <div class="icon-item"><i class="bi bi-people-fill"></i><span>bi-people</span></div>
            <div class="icon-item"><i class="bi bi-person-fill"></i><span>bi-person</span></div>
            <div class="icon-item"><i class="bi bi-person-circle"></i><span>bi-person-circle</span></div>
            <div class="icon-item"><i class="bi bi-person-badge"></i><span>bi-person-badge</span></div>
            <div class="icon-item"><i class="bi bi-chat-dots-fill"></i><span>bi-chat-dots</span></div>
            <div class="icon-item"><i class="bi bi-chat-fill"></i><span>bi-chat</span></div>
            <div class="icon-item"><i class="bi bi-envelope-fill"></i><span>bi-envelope</span></div>
            <div class="icon-item"><i class="bi bi-send-fill"></i><span>bi-send</span></div>
            <div class="icon-item"><i class="bi bi-telephone-fill"></i><span>bi-telephone</span></div>
            <div class="icon-item"><i class="bi bi-camera-video"></i><span>bi-camera</span></div>
            <div class="icon-item"><i class="bi bi-mic-fill"></i><span>bi-mic</span></div>
            <div class="icon-item"><i class="bi bi-megaphone-fill"></i><span>bi-megaphone</span></div>
            <div class="icon-item"><i class="bi bi-at"></i><span>bi-at</span></div>
            <div class="icon-item"><i class="bi bi-link-45deg"></i><span>bi-link</span></div>
            <div class="icon-item"><i class="bi bi-share"></i><span>bi-share</span></div>
            <div class="icon-item"><i class="bi bi-flag-fill"></i><span>bi-flag</span></div>
          </div>
        </div>
      </div>

      <!-- Status & Alerts -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Status &amp; Alerts</h6></div>
        <div class="card-body">
          <div class="icon-grid">
            <div class="icon-item"><i class="bi bi-check-circle-fill" style="color:var(--success);"></i><span>check-circle</span></div>
            <div class="icon-item"><i class="bi bi-exclamation-circle-fill" style="color:var(--danger);"></i><span>exclamation</span></div>
            <div class="icon-item"><i class="bi bi-exclamation-triangle-fill" style="color:var(--warning);"></i><span>triangle</span></div>
            <div class="icon-item"><i class="bi bi-info-circle-fill" style="color:var(--info);"></i><span>info-circle</span></div>
            <div class="icon-item"><i class="bi bi-question-circle-fill"></i><span>question</span></div>
            <div class="icon-item"><i class="bi bi-shield-fill-check" style="color:var(--success);"></i><span>shield-check</span></div>
            <div class="icon-item"><i class="bi bi-shield-fill-exclamation" style="color:var(--danger);"></i><span>shield-excl</span></div>
            <div class="icon-item"><i class="bi bi-eye-fill"></i><span>bi-eye</span></div>
            <div class="icon-item"><i class="bi bi-lock-fill"></i><span>bi-lock</span></div>
            <div class="icon-item"><i class="bi bi-unlock-fill"></i><span>bi-unlock</span></div>
            <div class="icon-item"><i class="bi bi-key-fill"></i><span>bi-key</span></div>
            <div class="icon-item"><i class="bi bi-shield-check"></i><span>bi-shield</span></div>
          </div>
        </div>
      </div>

      <!-- Finance & Business -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Finance &amp; Business</h6></div>
        <div class="card-body">
          <div class="icon-grid">
            <div class="icon-item"><i class="bi bi-currency-dollar"></i><span>currency-dollar</span></div>
            <div class="icon-item"><i class="bi bi-credit-card-2-front-fill"></i><span>credit-card</span></div>
            <div class="icon-item"><i class="bi bi-wallet-fill"></i><span>bi-wallet</span></div>
            <div class="icon-item"><i class="bi bi-cash-stack"></i><span>cash-stack</span></div>
            <div class="icon-item"><i class="bi bi-pie-chart-fill"></i><span>pie-chart</span></div>
            <div class="icon-item"><i class="bi bi-bar-chart-fill"></i><span>bar-chart</span></div>
            <div class="icon-item"><i class="bi bi-graph-up-arrow"></i><span>graph-up</span></div>
            <div class="icon-item"><i class="bi bi-receipt"></i><span>bi-receipt</span></div>
            <div class="icon-item"><i class="bi bi-shop"></i><span>bi-shop</span></div>
            <div class="icon-item"><i class="bi bi-tags-fill"></i><span>bi-tags</span></div>
            <div class="icon-item"><i class="bi bi-percent"></i><span>bi-percent</span></div>
            <div class="icon-item"><i class="bi bi-box-seam"></i><span>box-seam</span></div>
          </div>
        </div>
      </div>

      <!-- Media & Files -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Media &amp; Files</h6></div>
        <div class="card-body">
          <div class="icon-grid">
            <div class="icon-item"><i class="bi bi-image-fill"></i><span>bi-image</span></div>
            <div class="icon-item"><i class="bi bi-camera-fill"></i><span>bi-camera</span></div>
            <div class="icon-item"><i class="bi bi-play-circle-fill"></i><span>play-circle</span></div>
            <div class="icon-item"><i class="bi bi-music-note-beamed"></i><span>music-note</span></div>
            <div class="icon-item"><i class="bi bi-file-earmark-pdf-fill" style="color:var(--danger);"></i><span>pdf</span></div>
            <div class="icon-item"><i class="bi bi-file-earmark-image-fill" style="color:var(--success);"></i><span>image</span></div>
            <div class="icon-item"><i class="bi bi-file-earmark-code-fill" style="color:var(--accent-cyan);"></i><span>code-file</span></div>
            <div class="icon-item"><i class="bi bi-file-earmark-zip-fill"></i><span>zip</span></div>
            <div class="icon-item"><i class="bi bi-cloud-upload-fill"></i><span>cloud-upload</span></div>
            <div class="icon-item"><i class="bi bi-cloud-check-fill"></i><span>cloud-check</span></div>
            <div class="icon-item"><i class="bi bi-database-fill"></i><span>bi-database</span></div>
            <div class="icon-item"><i class="bi bi-server"></i><span>bi-server</span></div>
            <div class="icon-item"><i class="bi bi-printer-fill"></i><span>bi-printer</span></div>
            <div class="icon-item"><i class="bi bi-qr-code"></i><span>qr-code</span></div>
            <div class="icon-item"><i class="bi bi-code-slash"></i><span>code-slash</span></div>
            <div class="icon-item"><i class="bi bi-terminal-fill"></i><span>bi-terminal</span></div>
          </div>
        </div>
      </div>

      <!-- Penggunaan -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Cara Penggunaan</h6></div>
        <div class="card-body">
          <p class="text-secondary-c mb-2" style="font-size:0.88rem;">Bootstrap Icons menggunakan format class <code>bi bi-&lt;nama&gt;</code>. Ukuran dan warna bisa diatur via CSS seperti teks biasa.</p>
          <pre class="code-block">&lt;!-- Ikon dasar --&gt;
&lt;i class="bi bi-star-fill"&gt;&lt;/i&gt;

&lt;!-- Ukuran (via CSS font-size) --&gt;
&lt;i class="bi bi-star-fill" style="font-size:1.5rem;"&gt;&lt;/i&gt;

&lt;!-- Warna --&gt;
&lt;i class="bi bi-star-fill" style="color:var(--warning);"&gt;&lt;/i&gt;

&lt;!-- Dalam tombol --&gt;
&lt;button class="btn btn-primary-grad"&gt;
  &lt;i class="bi bi-plus-lg"&gt;&lt;/i&gt; Tambah Data
&lt;/button&gt;

&lt;!-- Ikon saja (button icon square) --&gt;
&lt;button class="btn btn-outline-soft btn-icon-sq"&gt;
  &lt;i class="bi bi-three-dots"&gt;&lt;/i&gt;
&lt;/button&gt;

&lt;!-- Referensi lengkap: https://icons.getbootstrap.com --&gt;</pre>
        </div>
      </div>
@endsection
