@extends('docs.layouts.app')

@section('title', 'UI Components')

@php $activeMenu = 'ui-components' @endphp

@push('styles')
<style>
  .comp-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1rem; }
  .comp-section { margin-bottom:2.5rem; }
  .comp-section h3 { font-size:1.1rem; margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid var(--border-subtle); }
  .comp-box { padding:1rem; border:1px solid var(--border-subtle); border-radius:var(--radius-md); background:var(--bg-surface); }
</style>
@endpush

@section('content')
      <div class="page-header">
        <div>
          <h1>UI Components Showcase</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Components</span></div>
        </div>
      </div>

      <!-- ====== 1. BUTTONS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-mouse2-fill me-2" style="color:var(--accent-cyan);"></i>Buttons</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-primary-grad">Primary</button>
            <button class="btn btn-success">Success</button>
            <button class="btn btn-danger">Danger</button>
            <button class="btn btn-warning">Warning</button>
            <button class="btn btn-info">Info</button>
            <button class="btn btn-outline-soft">Outline</button>
            <button class="btn btn-ghost">Ghost</button>
          </div>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-primary-grad btn-sm">Small</button>
            <button class="btn btn-primary-grad">Default</button>
            <button class="btn btn-primary-grad btn-lg">Large</button>
            <button class="btn btn-primary-grad btn-block" style="max-width:200px;">Block</button>
          </div>
          <div class="d-flex flex-wrap gap-2 mb-3">
            <button class="btn btn-primary-grad btn-loading">Loading</button>
            <button class="btn btn-outline-soft btn-icon-sq"><i class="bi bi-three-dots"></i></button>
            <button class="btn btn-outline-soft btn-icon-lg"><i class="bi bi-star"></i></button>
            <button class="btn btn-outline-soft btn-icon-circle btn-icon-lg"><i class="bi bi-heart"></i></button>
          </div>
          <div class="d-flex flex-wrap gap-2">
            <div class="btn-group">
              <button class="btn btn-primary-grad">Left</button>
              <button class="btn btn-primary-grad">Center</button>
              <button class="btn btn-primary-grad">Right</button>
            </div>
            <button class="btn btn-primary-grad" disabled>Disabled</button>
          </div>
        </div>
      </div>

      <!-- ====== 2. BADGES & PILLS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-bookmark-fill me-2" style="color:var(--success);"></i>Badges &amp; Pills</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="pill pill-success"><i class="bi bi-check-circle"></i> Sukses</span>
            <span class="pill pill-danger"><i class="bi bi-x-circle"></i> Gagal</span>
            <span class="pill pill-warning"><i class="bi bi-clock"></i> Pending</span>
            <span class="pill pill-info"><i class="bi bi-info-circle"></i> Info</span>
            <span class="pill pill-neutral">Netral</span>
          </div>
          <div class="d-flex flex-wrap gap-2">
            <span class="badge badge-primary">Primary</span>
            <span class="badge badge-success">Success</span>
            <span class="badge badge-danger">Danger</span>
            <span class="badge badge-warning">Warning</span>
            <span class="badge badge-info badge-pill">Info Pill</span>
            <span class="badge badge-danger badge-pulse">Live</span>
          </div>
        </div>
      </div>

      <!-- ====== 3. AVATARS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-person-fill me-2" style="color:var(--accent-1);"></i>Avatars</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <span class="avatar avatar-xs">RA</span>
            <span class="avatar avatar-sm">RA</span>
            <span class="avatar avatar-md">RA</span>
            <span class="avatar avatar-lg">RA</span>
            <span class="avatar avatar-xl">RA</span>
          </div>
          <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="avatar-group">
              <span class="avatar avatar-sm" style="background:#6366F1;">AD</span>
              <span class="avatar avatar-sm" style="background:#34D399;">MS</span>
              <span class="avatar avatar-sm" style="background:#F87171;">RH</span>
              <span class="avatar-more">+2</span>
            </div>
            <span class="avatar avatar-md">RA<span class="avatar-status online"></span></span>
            <span class="avatar avatar-md" style="background:#F87171;">BP<span class="avatar-status busy"></span></span>
            <span class="avatar avatar-md" style="background:#FBBF24;">DK<span class="avatar-status away"></span></span>
          </div>
        </div>
      </div>

      <!-- ====== 4. SPINNERS & LOADERS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-arrow-repeat me-2" style="color:var(--accent-cyan);"></i>Spinners &amp; Loaders</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <div class="spinner spinner-sm"></div>
            <div class="spinner"></div>
            <div class="spinner spinner-lg"></div>
            <div class="spinner-dots"><span></span><span></span><span></span></div>
          </div>
          <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="skeleton skeleton-avatar"></div>
            <div style="flex:1;max-width:300px;">
              <div class="skeleton skeleton-heading"></div>
              <div class="skeleton skeleton-text"></div>
              <div class="skeleton skeleton-text"></div>
            </div>
            <div class="skeleton skeleton-button"></div>
          </div>
          <div class="mt-3">
            <div style="position:relative;width:100%;max-width:300px;">
              <div class="form-loading" style="padding:1rem;border:1px solid var(--border-subtle);border-radius:var(--radius-sm);">
                <p class="text-secondary-c" style="font-size:0.85rem;">Form content here (loading overlay)</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 5. PROGRESS & RING ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-pie-chart-fill me-2" style="color:var(--success);"></i>Progress</h3>
        <div class="comp-box">
          <div class="mb-3" style="max-width:300px;">
            <div class="progress-label"><span class="progress-label-text">Progress</span><span class="progress-label-value">72%</span></div>
            <div class="progress-modern"><div class="bar" style="width:72%;"></div></div>
          </div>
          <div class="d-flex flex-wrap align-items-center gap-4">
            <div class="progress-ring ring-success" data-pct="75">
              <svg width="72" height="72"><circle class="ring-bg" cx="36" cy="36" r="30" stroke-width="5"></circle><circle class="ring-fg" cx="36" cy="36" r="30" stroke-width="5" fill="none"></circle></svg>
              <span class="ring-label">75%</span>
            </div>
            <div class="progress-ring ring-info" data-pct="45">
              <svg width="72" height="72"><circle class="ring-bg" cx="36" cy="36" r="30" stroke-width="5"></circle><circle class="ring-fg" cx="36" cy="36" r="30" stroke-width="5" fill="none"></circle></svg>
              <span class="ring-label">45%</span>
            </div>
            <div class="progress-ring ring-warning" data-pct="30">
              <svg width="72" height="72"><circle class="ring-bg" cx="36" cy="36" r="30" stroke-width="5"></circle><circle class="ring-fg" cx="36" cy="36" r="30" stroke-width="5" fill="none"></circle></svg>
              <span class="ring-label">30%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 6. FORMS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-ui-checks me-2" style="color:var(--warning);"></i>Forms</h3>
        <div class="comp-box">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label-modern">Text Input</label>
              <input type="text" class="form-control-modern" placeholder="nama@email.com">
            </div>
            <div class="col-md-4">
              <label class="form-label-modern">Select</label>
              <select class="form-select-modern"><option>Administrator</option><option>Editor</option><option>Viewer</option></select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-3">
              <label class="switch-modern"><input type="checkbox" checked><span class="switch-track"></span></label>
              <label class="form-check-modern"><input type="checkbox"><span style="font-size:0.85rem;">Remember me</span></label>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 7. TABS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-layout-three-columns me-2" style="color:var(--accent-1);"></i>Tabs</h3>
        <div class="comp-box">
          <div class="tabs-modern">
            <span class="tab-link active" data-tab-target="#tabDemo1">Tab Satu</span>
            <span class="tab-link" data-tab-target="#tabDemo2">Tab Dua</span>
            <span class="tab-link" data-tab-target="#tabDemo3">Tab Tiga</span>
          </div>
          <div id="tabDemo1" data-tab-panel><p class="text-secondary-c" style="font-size:0.85rem;">Konten tab satu — lorem ipsum dolor sit amet.</p></div>
          <div id="tabDemo2" data-tab-panel style="display:none;"><p class="text-secondary-c" style="font-size:0.85rem;">Konten tab dua — consectetur adipiscing elit.</p></div>
          <div id="tabDemo3" data-tab-panel style="display:none;"><p class="text-secondary-c" style="font-size:0.85rem;">Konten tab tiga — sed do eiusmod tempor incididunt.</p></div>
        </div>
      </div>

      <!-- ====== 8. ALERTS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-exclamation-triangle-fill me-2" style="color:var(--danger);"></i>Alerts &amp; Callout</h3>
        <div class="comp-box">
          <div class="d-flex flex-column gap-2 mb-3">
            <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><div class="alert-content"><div class="alert-title">Berhasil</div>Data berhasil disimpan.</div></div>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i><div class="alert-content">Gagal memuat data.<button class="alert-close">&times;</button></div></div>
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill"></i><div class="alert-content">Peringatan: kuota hampir habis.</div></div>
            <div class="alert alert-info"><i class="bi bi-info-circle-fill"></i><div class="alert-content">Info: pembaruan tersedia.</div></div>
          </div>
          <div class="d-flex flex-column gap-2">
            <div class="callout callout-info"><div class="callout-title"><i class="bi bi-info-circle-fill"></i> Informasi</div><div class="callout-body">Ini adalah callout info dengan border kiri biru.</div></div>
            <div class="callout callout-success"><div class="callout-title"><i class="bi bi-check-circle-fill"></i> Sukses</div><div class="callout-body">Operasi berjalan lancar.</div></div>
          </div>
        </div>
      </div>

      <!-- ====== 9. ACCORDION ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-chevron-expand me-2" style="color:var(--accent-cyan);"></i>Accordion</h3>
        <div class="comp-box">
          <div class="accordion-modern" style="max-width:500px;">
            <div class="accordion-item">
              <div class="accordion-header open" data-accordion-toggle><i class="bi bi-envelope-fill"></i> Apakah ini template gratis? <i class="bi bi-chevron-down"></i></div>
              <div class="accordion-body open">Ya, template ini gratis dan open-source. Anda bisa menggunakannya untuk proyek pribadi maupun komersial.</div>
            </div>
            <div class="accordion-item">
              <div class="accordion-header" data-accordion-toggle><i class="bi bi-gear-fill"></i> Bagaimana cara kustomisasi? <i class="bi bi-chevron-down"></i></div>
              <div class="accordion-body">CSS variable di :root memudahkan kustomisasi. Ubah nilai di main.css sesuai kebutuhan.</div>
            </div>
            <div class="accordion-item">
              <div class="accordion-header" data-accordion-toggle><i class="bi bi-code-slash"></i> Butuh framework? <i class="bi bi-chevron-down"></i></div>
              <div class="accordion-body">Tidak perlu. Template ini vanilla HTML/CSS/JS — bisa diintegrasikan ke framework apa pun.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 10. TIMELINE ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-clock-history me-2" style="color:var(--info);"></i>Timeline</h3>
        <div class="comp-box">
          <ul class="timeline" style="max-width:400px;">
            <li class="timeline-item">
              <span class="timeline-dot success"><i class="bi bi-check"></i></span>
              <div class="timeline-content"><h6>Deploy Selesai</h6><p>Production server v2.4.1 berhasil di-deploy.</p><span class="timeline-time">21 Jun 2026, 09:30</span></div>
            </li>
            <li class="timeline-item">
              <span class="timeline-dot"><i class="bi bi-circle-fill"></i></span>
              <div class="timeline-content"><h6>Database Backup</h6><p>Backup otomatis database selesai.</p><span class="timeline-time">21 Jun 2026, 08:00</span></div>
            </li>
            <li class="timeline-item">
              <span class="timeline-dot warning"><i class="bi bi-exclamation"></i></span>
              <div class="timeline-content"><h6>CPU Warning</h6><p>Penggunaan CPU mencapai 85% pada server staging.</p><span class="timeline-time">20 Jun 2026, 23:15</span></div>
            </li>
          </ul>
        </div>
      </div>

      <!-- ====== 11. PAGINATION ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-list-ol me-2" style="color:var(--success);"></i>Pagination</h3>
        <div class="comp-box">
          <ul class="pagination-modern">
            <li class="disabled"><span>&laquo;</span></li>
            <li class="active"><a href="#">1</a></li>
            <li><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><span>...</span></li>
            <li><a href="#">12</a></li>
            <li><a href="#">&raquo;</a></li>
          </ul>
        </div>
      </div>

      <!-- ====== 12. LIST GROUP ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-list-ul me-2" style="color:var(--accent-1);"></i>List Group</h3>
        <div class="comp-box">
          <ul class="list-group-modern" style="max-width:350px;">
            <li class="active"><span class="list-group-icon"><i class="bi bi-person-fill"></i></span><div><div class="list-group-primary">Profil Saya</div><div class="list-group-secondary">Pengaturan akun pribadi</div></div></li>
            <li><span class="list-group-icon"><i class="bi bi-lock-fill"></i></span><div><div class="list-group-primary">Keamanan</div><div class="list-group-secondary">Password &amp; 2FA</div></div></li>
            <li><span class="list-group-icon"><i class="bi bi-bell-fill"></i></span><div><div class="list-group-primary">Notifikasi</div><div class="list-group-secondary">Preferensi notifikasi</div></div></li>
          </ul>
        </div>
      </div>

      <!-- ====== 13. STEPS / STEPPER ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-signpost-2-fill me-2" style="color:var(--warning);"></i>Stepper</h3>
        <div class="comp-box">
          <div class="steps-modern">
            <div class="step-item completed"><div class="step-number"><i class="bi bi-check"></i></div><div class="step-label">Akun</div></div>
            <div class="step-item active"><div class="step-number">2</div><div class="step-label">Profil</div></div>
            <div class="step-item"><div class="step-number">3</div><div class="step-label">Verifikasi</div></div>
            <div class="step-item"><div class="step-number">4</div><div class="step-label">Selesai</div></div>
          </div>
        </div>
      </div>

      <!-- ====== 14. TAGS / CHIPS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-tags-fill me-2" style="color:var(--accent-cyan);"></i>Tags / Chips</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap gap-2 mb-3">
            <span class="tag">frontend <i class="bi bi-x"></i></span>
            <span class="tag tag-primary">produksi <i class="bi bi-x"></i></span>
            <span class="tag tag-success">selesai <i class="bi bi-x"></i></span>
            <span class="tag tag-danger">urgent <i class="bi bi-x"></i></span>
            <span class="chip-tag">#design-system</span>
          </div>
          <div class="tag-input-area" style="max-width:350px;">
            <span class="tag">vue <i class="bi bi-x"></i></span>
            <span class="tag tag-primary">typescript <i class="bi bi-x"></i></span>
            <input type="text" placeholder="Tambah tag...">
          </div>
        </div>
      </div>

      <!-- ====== 15. DROPZONE ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-cloud-upload-fill me-2" style="color:var(--accent-1);"></i>Dropzone</h3>
        <div class="comp-box">
          <div class="dropzone" style="max-width:400px;">
            <i class="bi bi-cloud-arrow-up-fill"></i>
            <h6>Seret file ke sini</h6>
            <p>Mendukung PNG, JPG, PDF — maks 10MB</p>
          </div>
          <div class="dropzone-preview">
            <div class="dropzone-file"><i class="bi bi-file-earmark-pdf-fill"></i> laporan.pdf <i class="bi bi-x"></i></div>
            <div class="dropzone-file"><i class="bi bi-file-earmark-image-fill"></i> screenshot.png <i class="bi bi-x"></i></div>
          </div>
        </div>
      </div>

      <!-- ====== 16. RATING ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-star-fill me-2" style="color:#FBBF24;"></i>Rating</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap align-items-center gap-4">
            <div class="rating"><input type="radio" id="star5" name="rating"><label for="star5"></label><input type="radio" id="star4" name="rating"><label for="star4"></label><input type="radio" id="star3" name="rating" checked><label for="star3"></label><input type="radio" id="star2" name="rating"><label for="star2"></label><input type="radio" id="star1" name="rating"><label for="star1"></label></div>
            <div class="rating-static"><i class="bi bi-star-fill filled"></i><i class="bi bi-star-fill filled"></i><i class="bi bi-star-fill filled"></i><i class="bi bi-star-fill filled"></i><i class="bi bi-star"></i></div>
          </div>
        </div>
      </div>

      <!-- ====== 17. CARDS EXTENDED ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-credit-card-fill me-2" style="color:var(--success);"></i>Card Variants</h3>
        <div class="row g-3">
          <div class="col-md-4">
            <div class="card card-glow h-100"><div class="card-inner card-body"><h6>Card Glow</h6><p class="text-muted-c mb-0" style="font-size:0.82rem;">Hover untuk melihat efek border gradient.</p></div></div>
          </div>
          <div class="col-md-4">
            <div class="card card-hover-scale h-100"><div class="card-body"><h6>Card Hover Scale</h6><p class="text-muted-c mb-0" style="font-size:0.82rem;">Efek translateY dikombinasikan dengan shadow.</p></div></div>
          </div>
          <div class="col-md-4">
            <div class="card card-horizontal h-100"><div class="card-img-side"><i class="bi bi-image"></i></div><div class="card-body"><h6>Horizontal</h6><p class="text-muted-c mb-0" style="font-size:0.82rem;">Image + text side by side.</p></div></div>
          </div>
        </div>
      </div>

      <!-- ====== 18. CHAT BUBBLES ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-chat-dots-fill me-2" style="color:var(--accent-cyan);"></i>Chat UI</h3>
        <div class="comp-box">
          <div class="chat-thread mb-3" style="max-width:450px;">
            <div class="chat-bubble incoming">Halo, ada yang bisa saya bantu?<span class="chat-time">09:30</span></div>
            <div class="chat-bubble outgoing">Hai, saya ingin bertanya tentang API key.<span class="chat-time">09:31</span></div>
            <div class="chat-bubble incoming">Tentu, silakan cek di halaman Settings > API Keys.<span class="chat-time">09:32</span></div>
            <div class="chat-bubble outgoing">Terima kasih!<span class="chat-time">09:33</span></div>
          </div>
          <div class="chat-input-area" style="max-width:450px;">
            <input type="text" placeholder="Ketik pesan...">
            <button class="chat-send-btn"><i class="bi bi-send"></i></button>
          </div>
        </div>
      </div>

      <!-- ====== 19. CAROUSEL ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-images me-2" style="color:var(--accent-1);"></i>Carousel</h3>
        <div class="comp-box">
          <div class="carousel-modern" style="max-width:500px;" data-autoplay="4000">
            <div class="carousel-track">
              <div class="carousel-slide"><i class="bi bi-graph-up-arrow"></i><span>Dashboard Analytics</span></div>
              <div class="carousel-slide"><i class="bi bi-people-fill"></i><span>User Management</span></div>
              <div class="carousel-slide"><i class="bi bi-gear-fill"></i><span>Settings Panel</span></div>
            </div>
            <button class="carousel-btn carousel-prev"><i class="bi bi-chevron-left"></i></button>
            <button class="carousel-btn carousel-next"><i class="bi bi-chevron-right"></i></button>
            <div class="carousel-dots"><span class="active"></span><span></span><span></span></div>
          </div>
        </div>
      </div>

      <!-- ====== 20. COUNTDOWN ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-clock-fill me-2" style="color:var(--warning);"></i>Countdown Timer</h3>
        <div class="comp-box">
          <div class="countdown" data-target="2027-01-01T00:00:00">
            <div class="cd-block"><div class="cd-value" data-cd="days">00</div><span class="cd-label">Hari</span></div>
            <span class="cd-sep">:</span>
            <div class="cd-block"><div class="cd-value" data-cd="hours">00</div><span class="cd-label">Jam</span></div>
            <span class="cd-sep">:</span>
            <div class="cd-block"><div class="cd-value" data-cd="mins">00</div><span class="cd-label">Menit</span></div>
            <span class="cd-sep">:</span>
            <div class="cd-block"><div class="cd-value" data-cd="secs">00</div><span class="cd-label">Detik</span></div>
          </div>
        </div>
      </div>

      <!-- ====== 21. PRICE TOGGLE ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-currency-dollar me-2" style="color:var(--success);"></i>Price Toggle</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap align-items-center gap-3">
            <div class="price-toggle">
              <span class="pt-opt active">Monthly</span>
              <span class="pt-opt">Yearly <span class="price-save ms-1">Save 20%</span></span>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 22. TOOLTIP ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-chat-square-quote-fill me-2" style="color:var(--text-muted);"></i>Tooltip</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap gap-3">
            <div class="tooltip-wrapper"><span class="tooltip-trigger">Hover saya</span><span class="tooltip-content">Ini tooltip!</span></div>
            <div class="tooltip-wrapper"><span class="tooltip-trigger">Tooltip bawah</span><span class="tooltip-content bottom">Arah bawah</span></div>
          </div>
        </div>
      </div>

      <!-- ====== 23. METRIC CARDS ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-speedometer2 me-2" style="color:var(--accent-1);"></i>Metric Cards</h3>
        <div class="comp-box">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="card"><div class="card-body">
                <div class="metric-card">
                  <div class="metric-icon" style="background:var(--info-bg);color:var(--accent-cyan);"><i class="bi bi-cpu-fill"></i></div>
                  <div class="metric-info"><div class="metric-value">48.2k</div><div class="metric-label">API Requests</div></div>
                  <div class="metric-change up">+12.4%</div>
                </div>
              </div></div>
            </div>
            <div class="col-md-4">
              <div class="card"><div class="card-body">
                <div class="metric-card">
                  <div class="metric-icon" style="background:var(--success-bg);color:var(--success);"><i class="bi bi-people-fill"></i></div>
                  <div class="metric-info"><div class="metric-value">3,127</div><div class="metric-label">Active Users</div></div>
                  <div class="metric-change up">+4.8%</div>
                </div>
              </div></div>
            </div>
            <div class="col-md-4">
              <div class="card"><div class="card-body">
                <div class="metric-card">
                  <div class="metric-icon" style="background:var(--danger-bg);color:var(--danger);"><i class="bi bi-exclamation-triangle-fill"></i></div>
                  <div class="metric-info"><div class="metric-value">0.8%</div><div class="metric-label">Error Rate</div></div>
                  <div class="metric-change down">-0.3%</div>
                </div>
              </div></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 24. STAT RING ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-circle-fill me-2" style="color:var(--info);"></i>Stat Ring Card</h3>
        <div class="comp-box">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="card"><div class="card-body">
                <div class="stat-ring-card">
                  <div class="progress-ring ring-success" data-pct="82">
                    <svg width="72" height="72"><circle class="ring-bg" cx="36" cy="36" r="30" stroke-width="5"></circle><circle class="ring-fg" cx="36" cy="36" r="30" stroke-width="5" fill="none"></circle></svg>
                    <span class="ring-label">82%</span>
                  </div>
                  <div class="stat-ring-info"><div class="stat-ring-value">82%</div><div class="stat-ring-label">Uptime SLA</div><span class="stat-ring-change up">+2.1%</span></div>
                </div>
              </div></div>
            </div>
            <div class="col-md-6">
              <div class="card"><div class="card-body">
                <div class="stat-ring-card">
                  <div class="progress-ring ring-info" data-pct="64">
                    <svg width="72" height="72"><circle class="ring-bg" cx="36" cy="36" r="30" stroke-width="5"></circle><circle class="ring-fg" cx="36" cy="36" r="30" stroke-width="5" fill="none"></circle></svg>
                    <span class="ring-label">64%</span>
                  </div>
                  <div class="stat-ring-info"><div class="stat-ring-value">64%</div><div class="stat-ring-label">Storage Used</div><span class="stat-ring-change up">+8%</span></div>
                </div>
              </div></div>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 25. PROFILE HEADER ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-person-badge-fill me-2" style="color:var(--accent-1);"></i>Profile Header</h3>
        <div class="comp-box">
          <div class="profile-header">
            <div class="ph-avatar">RA</div>
            <div class="ph-info">
              <div class="ph-name">Rangga A.</div>
              <div class="ph-role">Administrator</div>
              <div class="ph-meta">
                <span><i class="bi bi-envelope-fill"></i> rangga@nexora.id</span>
                <span><i class="bi bi-telephone-fill"></i> +62 812 3456 7890</span>
                <span><i class="bi bi-geo-alt-fill"></i> Jakarta, Indonesia</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 26. INVOICE PREVIEW ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-receipt me-2" style="color:var(--success);"></i>Invoice Preview</h3>
        <div class="comp-box">
          <div class="invoice-preview" style="max-width:500px;">
            <div class="inv-header"><div><h4>Invoice #INV-2026-001</h4><div class="inv-meta">Issued: 21 Jun 2026</div></div><div><div class="brand-mark" style="margin-left:auto;">N</div></div></div>
            <table class="inv-table">
              <thead><tr><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
              <tbody><tr><td>Pro Plan - Monthly</td><td>1</td><td>$49.00</td></tr><tr><td>Storage Add-on (50GB)</td><td>2</td><td>$20.00</td></tr><tr><td>API Credits (10k)</td><td>1</td><td>$15.00</td></tr></tbody>
            </table>
            <div class="inv-total">$84.00</div>
          </div>
        </div>
      </div>

      <!-- ====== 27. EMPTY STATE ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-inbox-fill me-2" style="color:var(--text-muted);"></i>Empty State</h3>
        <div class="comp-box">
          <div class="empty-state text-center" style="padding:2rem;">
            <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
            <div class="empty-state-title">Belum ada data</div>
            <div class="empty-state-desc">Belum ada notifikasi masuk. Notifikasi akan muncul di sini.</div>
            <button class="btn btn-primary-grad btn-sm mt-2"><i class="bi bi-plus-lg"></i> Buat Baru</button>
          </div>
        </div>
      </div>

      <!-- ====== 28. KANBAN BOARD ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-kanban-fill me-2" style="color:var(--accent-cyan);"></i>Kanban Board</h3>
        <div class="comp-box">
          <div class="kanban-columns">
            <div class="kanban-column">
              <div class="kanban-col-header">To Do <span class="kanban-col-count">3</span></div>
              <div class="kanban-cards">
                <div class="kanban-card"><h6>Setup CI/CD</h6><p>Configure GitHub Actions pipeline</p><div class="kanban-card-footer"><span class="chip-tag">devops</span><span class="avatar avatar-xs" style="background:#6366F1;">AD</span></div></div>
                <div class="kanban-card"><h6>API Documentation</h6><p>Write OpenAPI spec for v2 endpoints</p><div class="kanban-card-footer"><span class="chip-tag">docs</span><span class="avatar avatar-xs" style="background:#34D399;">MS</span></div></div>
              </div>
            </div>
            <div class="kanban-column">
              <div class="kanban-col-header">In Progress <span class="kanban-col-count">2</span></div>
              <div class="kanban-cards">
                <div class="kanban-card"><h6>User Dashboard</h6><p>Build analytics widgets</p><div class="kanban-card-footer"><span class="chip-tag">frontend</span><span class="avatar avatar-xs" style="background:#F87171;">RH</span></div></div>
              </div>
            </div>
            <div class="kanban-column">
              <div class="kanban-col-header">Done <span class="kanban-col-count">1</span></div>
              <div class="kanban-cards">
                <div class="kanban-card"><h6>Auth System</h6><p>JWT login &amp; registration flow</p><div class="kanban-card-footer"><span class="chip-tag">backend</span><span class="avatar avatar-xs" style="background:#6366F1;">RA</span></div></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ====== 29. DRAWER ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-layout-sidebar me-2" style="color:var(--accent-1);"></i>Drawer / Offcanvas</h3>
        <div class="comp-box">
          <button class="btn btn-outline-soft" onclick="document.getElementById('demoDrawer').classList.add('open');document.getElementById('demoDrawerBackdrop').classList.add('open');">
            <i class="bi bi-arrow-left-square"></i> Buka Drawer
          </button>
          <div class="drawer-backdrop" id="demoDrawerBackdrop" onclick="this.classList.remove('open');document.getElementById('demoDrawer').classList.remove('open');"></div>
          <div class="drawer" id="demoDrawer">
            <div class="drawer-header"><h5>Drawer Panel</h5><button class="drawer-close" onclick="document.getElementById('demoDrawer').classList.remove('open');document.getElementById('demoDrawerBackdrop').classList.remove('open');"><i class="bi bi-x"></i></button></div>
            <div class="drawer-body"><p class="text-secondary-c" style="font-size:0.85rem;">Ini adalah drawer / offcanvas panel. Bisa digunakan untuk sidebar pengaturan, detail item, atau form tambahan.</p><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text"></div><div class="skeleton skeleton-text" style="width:60%;"></div></div>
          </div>
        </div>
      </div>

      <!-- ====== 30. MASONRY GRID ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-grid-3x3 me-2" style="color:var(--success);"></i>Masonry Grid</h3>
        <div class="comp-box">
          <div class="masonry-grid" style="max-width:500px;">
            <div class="card"><div class="card-body"><h6>Card 1</h6><p class="text-muted-c" style="font-size:0.82rem;">Konten pendek.</p></div></div>
            <div class="card"><div class="card-body"><h6>Card 2</h6><p class="text-muted-c" style="font-size:0.82rem;">Konten agak panjang dengan beberapa baris teks untuk testing masonry layout.</p></div></div>
            <div class="card"><div class="card-body"><h6>Card 3</h6><p class="text-muted-c" style="font-size:0.82rem;">Pendek lagi.</p></div></div>
            <div class="card"><div class="card-body"><h6>Card 4</h6><p class="text-muted-c" style="font-size:0.82rem;">Konten lebih panjang lagi untuk menunjukkan efek masonry yang rapi dengan kolom otomatis.</p></div></div>
            <div class="card"><div class="card-body"><h6>Card 5</h6><p class="text-muted-c" style="font-size:0.82rem;">Pendek.</p></div></div>
          </div>
        </div>
      </div>

      <!-- ====== 31. TABLE ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-table me-2" style="color:var(--accent-1);"></i>Table Modern</h3>
        <div class="comp-box">
          <div class="table-responsive">
            <table class="table-modern">
              <thead><tr><th>Name</th><th>Role</th><th>Status</th><th>Joined</th></tr></thead>
              <tbody>
                <tr><td class="cell-primary"><span class="avatar-sm">SA</span>Sinta Amalia</td><td>Admin</td><td><span class="pill pill-success">Active</span></td><td class="text-muted-c">02 Jan 2026</td></tr>
                <tr><td class="cell-primary"><span class="avatar-sm">BP</span>Budi Pratama</td><td>Editor</td><td><span class="pill pill-neutral">Inactive</span></td><td class="text-muted-c">14 Feb 2026</td></tr>
                <tr><td class="cell-primary"><span class="avatar-sm">RH</span>Rian Hidayat</td><td>Editor</td><td><span class="pill pill-warning">Pending</span></td><td class="text-muted-c">09 Apr 2026</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ====== 32. DIVIDER ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-hr me-2" style="color:var(--text-muted);"></i>Dividers</h3>
        <div class="comp-box">
          <div class="divider-label">Divider with Label</div>
          <div class="divider-line my-3"></div>
        </div>
      </div>

      <!-- ====== 33. TOAST TRIGGER ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-info-circle-fill me-2" style="color:var(--info);"></i>Toast Demo</h3>
        <div class="comp-box">
          <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary-grad" onclick="NexoraToast('Toast sukses!', 'success')">Toast Sukses</button>
            <button class="btn btn-danger" onclick="NexoraToast('Toast error!', 'danger')">Toast Error</button>
            <button class="btn btn-outline-soft" onclick="NexoraToast('Toast informasi.')">Toast Info</button>
          </div>
        </div>
      </div>

      <!-- ====== 34. CONTEXT MENU ====== -->
      <div class="comp-section">
        <h3><i class="bi bi-menu-button-wide-fill me-2" style="color:var(--warning);"></i>Context Menu</h3>
        <div class="comp-box">
          <p class="text-secondary-c" style="font-size:0.85rem;" data-context-menu="#demoContextMenu">Klik kanan di sini untuk membuka context menu</p>
          <div class="context-menu" id="demoContextMenu">
            <div class="ctx-item"><i class="bi bi-pencil"></i> Edit</div>
            <div class="ctx-item"><i class="bi bi-files"></i> Duplicate</div>
            <div class="ctx-item"><i class="bi bi-download"></i> Export</div>
            <div class="ctx-divider"></div>
            <div class="ctx-item danger"><i class="bi bi-trash"></i> Delete</div>
          </div>
        </div>
      </div>
@endsection
