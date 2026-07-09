@extends('docs.layouts.app')

@section('title', 'Dokumentasi')

@php $activeMenu = 'documentation' @endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism.min.css">
<style>
  .doc-shell { display:flex; }
  .doc-toc {
    width: 240px;
    flex-shrink: 0;
    position: sticky;
    top: calc(var(--topbar-h) + 1.75rem);
    height: fit-content;
    max-height: calc(100vh - var(--topbar-h) - 3rem);
    overflow-y: auto;
  }
  .doc-toc a {
    display:block;
    padding: 0.4rem 0.7rem;
    font-size: 0.82rem;
    color: var(--text-muted);
    border-radius: var(--radius-sm);
    border-left: 2px solid transparent;
  }
  .doc-toc a:hover { color: var(--text-primary); background: var(--bg-elevated); }
  .doc-toc a.active { color: var(--text-primary); border-left-color: var(--accent-1); background: var(--bg-elevated); }
  .doc-toc-group { font-size:0.68rem; text-transform:uppercase; letter-spacing:0.08em; color: var(--text-muted); font-weight:600; padding: 1rem 0.7rem 0.4rem; }

  .doc-main { flex: 1; min-width: 0; padding-left: 2rem; }
  .doc-section { margin-bottom: 3rem; scroll-margin-top: 90px; }
  .doc-section h2 {
    font-size: 1.35rem;
    margin-bottom: 0.4rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-subtle);
  }
  .doc-section > p.lead-desc { color: var(--text-secondary); font-size: 0.92rem; margin: 0.75rem 0 1.25rem; max-width: 720px; }
  .doc-sub { margin-bottom: 2rem; }
  .doc-sub h4 { font-size: 1rem; margin-bottom: 0.4rem; }
  .doc-sub p.sub-desc { color: var(--text-muted); font-size: 0.84rem; margin-bottom: 0.9rem; }

  .doc-preview {
    background: var(--bg-surface);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-md) var(--radius-md) 0 0;
    padding: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    align-items: center;
  }
  .doc-code-wrap { position: relative; }
  .doc-code-wrap pre {
    margin: 0;
    border-radius: 0 0 var(--radius-md) var(--radius-md);
    font-size: 0.78rem;
    max-height: 320px;
    background: var(--bg-elevated) !important;
    border: 1px solid var(--border-subtle);
    border-top: none;
    text-shadow: none;
  }
  .doc-code-wrap pre code {
    background: transparent !important;
    color: var(--text-secondary);
    text-shadow: none;
  }
  .token.comment,
  .token.prolog,
  .token.doctype,
  .token.cdata {
    color: var(--text-muted) !important;
  }
  .token.punctuation {
    color: var(--text-muted) !important;
  }
  .token.tag,
  .token.keyword,
  .token.selector,
  .token.atrule {
    color: #6366F1 !important;
  }
  .token.attr-name,
  .token.function,
  .token.builtin {
    color: #22D3EE !important;
  }
  .token.string,
  .token.attr-value,
  .token.char {
    color: #34D399 !important;
  }
  .token.number,
  .token.boolean,
  .token.constant {
    color: #F87171 !important;
  }
  .token.operator,
  .token.entity,
  .token.url {
    color: #FBBF24 !important;
  }
  .token.property,
  .token.variable {
    color: var(--text-primary) !important;
  }

  .doc-copy-btn {
    position: absolute;
    top: 0.6rem;
    right: 0.6rem;
    background: var(--bg-elevated-2);
    border: 1px solid var(--border-subtle);
    color: var(--text-secondary);
    font-size: 0.72rem;
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    cursor: pointer;
    z-index: 2;
    transition: all var(--transition);
  }
  .doc-copy-btn:hover {
    color: var(--accent-1);
    border-color: var(--accent-1);
  }

  .doc-table th, .doc-table td { font-size: 0.83rem; }
  .doc-search-box { position: relative; margin-bottom: 0.5rem; }

  @media (max-width: 991px) {
    .doc-toc { display: none; }
    .doc-main { padding-left: 0; }
  }
</style>
@endpush

@section('content')
      <div class="page-header">
        <div>
          <h1>Dokumentasi</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Dokumentasi</span></div>
        </div>
      </div>

      <div class="doc-shell">
        <!-- TABLE OF CONTENTS -->
        <nav class="doc-toc scroll-thin" id="docToc">
          <div class="doc-toc-group">Memulai</div>
          <a href="#pengenalan" class="active">Pengenalan</a>
          <a href="#struktur-file">Struktur File</a>
          <a href="#cara-pakai">Cara Memulai</a>
          <a href="#design-tokens">Design Tokens</a>

          <div class="doc-toc-group">Layout</div>
          <a href="#layout-sidebar">Sidebar</a>
          <a href="#layout-topbar">Topbar</a>

          <div class="doc-toc-group">Komponen</div>
          <a href="#comp-buttons">Buttons</a>
          <a href="#comp-cards">Cards</a>
          <a href="#comp-pills">Pills &amp; Badge</a>
          <a href="#comp-table">Table</a>
          <a href="#comp-forms">Form Elements</a>
          <a href="#comp-tabs">Tabs</a>
          <a href="#comp-modal">Modal</a>
          <a href="#comp-toast">Toast</a>
          <a href="#comp-chart">Charts</a>
          <a href="#comp-chart">— Dashboard Charts</a>
          <a href="#comp-chart">— 10 Chart Modern</a>

          <div class="doc-toc-group">Komponen Baru ✦</div>
          <a href="#comp-alerts">Alerts</a>
          <a href="#comp-avatars">Avatars</a>
          <a href="#comp-timeline">Timeline</a>
          <a href="#comp-pagination">Pagination</a>
          <a href="#comp-spinners">Spinners &amp; Skeleton</a>
          <a href="#comp-accordion">Accordion</a>
          <a href="#comp-dropzone">Dropzone &amp; Tags</a>
          <a href="#comp-listgroup">List Group</a>
          <a href="#comp-misc">Callout, Steps, Rating</a>

          <div class="doc-toc-group">Lainnya</div>
          <a href="#js-api">JavaScript API</a>
          <a href="#faq">FAQ</a>
        </nav>

        <!-- MAIN DOC CONTENT -->
        <div class="doc-main">

          <!-- PENGENALAN -->
          <section class="doc-section" id="pengenalan">
            <h2>Pengenalan</h2>
            <p class="lead-desc">
              <strong>Nexora Admin</strong> adalah template dashboard admin berbasis Bootstrap 5 dengan tema gelap modern,
              dirancang untuk platform AI SaaS, panel analitik, dan sistem manajemen internal. Template ini dibangun
              murni dengan HTML, CSS, dan JavaScript vanilla — tidak memerlukan proses build apa pun, sehingga bisa
              langsung dijalankan di browser atau diintegrasikan ke backend apa pun (Laravel, Express, Django, dll).
            </p>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="card card-glow h-100"><div class="card-inner card-body">
                  <i class="bi bi-lightning-charge-fill" style="color:var(--accent-cyan); font-size:1.3rem;"></i>
                  <h6 class="mt-2 mb-1">Tanpa Build Tools</h6>
                  <p class="text-muted-c mb-0" style="font-size:0.82rem;">Buka langsung di browser, tidak perlu npm install.</p>
                </div></div>
              </div>
              <div class="col-md-4">
                <div class="card card-glow h-100"><div class="card-inner card-body">
                  <i class="bi bi-moon-stars-fill" style="color:var(--accent-2); font-size:1.3rem;"></i>
                  <h6 class="mt-2 mb-1">Dark &amp; Light Mode</h6>
                  <p class="text-muted-c mb-0" style="font-size:0.82rem;">Tema otomatis tersimpan di localStorage pengguna.</p>
                </div></div>
              </div>
              <div class="col-md-4">
                <div class="card card-glow h-100"><div class="card-inner card-body">
                  <i class="bi bi-puzzle-fill" style="color:var(--success); font-size:1.3rem;"></i>
                  <h6 class="mt-2 mb-1">Komponen Modular</h6>
                  <p class="text-muted-c mb-0" style="font-size:0.82rem;">Setiap komponen menggunakan class CSS independen.</p>
                </div></div>
              </div>
            </div>
          </section>

          <!-- NEW: ADDITIONS -->
          <section class="doc-section" id="comp-new">
            <h2>Fitur Tambahan</h2>
            <p class="lead-desc">Bagian ini menjelaskan fitur dan komponen baru yang ditambahkan: pengaturan panel dinamis, flyout sidebar saat collapsed, peningkatan tampilan <code>select</code>, dan integrasi DataTable untuk tabel interaktif.</p>

            <div class="doc-sub">
              <h4>Settings Page (panel dinamis)</h4>
              <p class="sub-desc">Pada <code>settings.html</code> menu kiri sekarang menggunakan atribut <code>data-settings-target</code>. Klik menu akan menampilkan panel yang sesuai di sisi kanan tanpa reload.</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeSettings"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeSettings"><code class="language-html">&lt;li class="nav-item"&gt;&lt;a class="nav-link" data-settings-target="#settings-account"&gt;Akun&lt;/a&gt;&lt;/li&gt;
&lt;div id="settings-account" class="settings-panel"&gt;...&lt;/div&gt;</code></pre>
              </div>
            </div>

            <div class="doc-sub">
              <h4>Sidebar Flyout</h4>
              <p class="sub-desc">Jika sidebar dilipat (<code>.is-collapsed</code>), hover pada item akan menampilkan flyout dengan nama menu dan submenu (jika ada). Logika ini ada di <code>index.js</code> dan dimatikan pada layar kecil.</p>
            </div>

            <div class="doc-sub">
              <h4>Peningkatan Select</h4>
              <p class="sub-desc">Class <code>form-select-modern</code> kini memiliki styling custom caret dan padding yang lebih baik agar cocok dengan desain template.</p>
            </div>

            <div class="doc-sub">
              <h4>DataTable (Tabel Interaktif)</h4>
              <p class="sub-desc">Tabel pada <code>tables.html</code> menggunakan <code>simple-datatables</code> (vanilla, via CDN) untuk search, sort, dan pagination. Inisialisasinya ada di bagian bawah <code>tables.html</code>.</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeDataTable"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeDataTable"><code class="language-html">&lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css"&gt;
                &lt;script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" defer&gt;&lt;/script&gt;
                &lt;script&gt;new simpleDatatables.DataTable('#usersTableInteractive');&lt;/script&gt;</code></pre>
              </div>
            </div>
          </section>

          <!-- STRUKTUR FILE -->
          <section class="doc-section" id="struktur-file">
            <h2>Struktur File</h2>
            <p class="lead-desc">Berikut struktur folder template ini. Semua halaman berada di root, sedangkan aset (CSS, JS, gambar) berada di folder <code>assets/</code>.</p>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeStruktur"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeStruktur"><code class="language-bash">admin-template/
├── index.html              # Dashboard utama
├── analytics.html          # Halaman analitik
├── users.html              # Manajemen pengguna
├── components.html         # Showcase semua komponen UI
├── forms.html              # Contoh elemen form
├── tables.html             # Contoh tabel data
├── settings.html           # Halaman pengaturan
├── auth-login.html         # Halaman login
├── documentation.html      # Halaman ini
└── assets/
    ├── css/
    │   └── main.css         # Seluruh style & design tokens
    ├── js/
    │   └── index.js         # Logika interaktif (sidebar, tema, chart, dll)
    └── img/                 # Tempat menyimpan gambar/logo Anda</code></pre>
            </div>
          </section>

          <!-- CARA MEMULAI -->
          <section class="doc-section" id="cara-pakai">
            <h2>Cara Memulai</h2>
            <p class="lead-desc">Tidak ada proses instalasi. Cukup ekstrak file dan buka <code>index.html</code> di browser, atau upload ke server/hosting Anda.</p>
            <div class="doc-sub">
              <h4>1. Menjalankan secara lokal</h4>
              <p class="sub-desc">Disarankan menggunakan local server sederhana agar path relatif (CSS/JS) berjalan optimal.</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeRunLocal"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeRunLocal"><code class="language-bash"># Dengan Python
python -m http.server 8080

# Atau dengan Node.js (npx serve)
npx serve .</code></pre>
              </div>
            </div>
            <div class="doc-sub">
              <h4>2. Menyesuaikan nama brand</h4>
              <p class="sub-desc">Ubah teks dan inisial pada elemen <code>.sidebar-brand</code> di setiap halaman, atau lakukan find-and-replace global untuk teks "Nexora".</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeBrand"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeBrand"><code class="language-html">&lt;div class="sidebar-brand"&gt;
  &lt;div class="brand-mark"&gt;N&lt;/div&gt;
  &lt;span class="brand-name"&gt;Nexora&lt;/span&gt;
&lt;/div&gt;</code></pre>
              </div>
            </div>
          </section>

          <!-- DESIGN TOKENS -->
          <section class="doc-section" id="design-tokens">
            <h2>Design Tokens</h2>
            <p class="lead-desc">Seluruh warna, radius, dan transisi didefinisikan sebagai CSS variable di <code>:root</code> pada <code>main.css</code>. Ubah nilai di sini untuk mengubah seluruh tema secara konsisten.</p>
            <div class="row g-2 mb-3">
              <div class="col-6 col-md-3"><div class="card"><div class="card-body p-2 text-center">
                <div style="height:40px; border-radius:8px; background:var(--accent-1);"></div>
                <small class="text-mono d-block mt-1">--accent-1</small>
              </div></div></div>
              <div class="col-6 col-md-3"><div class="card"><div class="card-body p-2 text-center">
                <div style="height:40px; border-radius:8px; background:var(--accent-2);"></div>
                <small class="text-mono d-block mt-1">--accent-2</small>
              </div></div></div>
              <div class="col-6 col-md-3"><div class="card"><div class="card-body p-2 text-center">
                <div style="height:40px; border-radius:8px; background:var(--accent-cyan);"></div>
                <small class="text-mono d-block mt-1">--accent-cyan</small>
              </div></div></div>
              <div class="col-6 col-md-3"><div class="card"><div class="card-body p-2 text-center">
                <div style="height:40px; border-radius:8px; background:var(--bg-elevated-2); border:1px solid var(--border-strong);"></div>
                <small class="text-mono d-block mt-1">--bg-elevated-2</small>
              </div></div></div>
            </div>
            <div class="table-responsive">
              <table class="table-modern doc-table">
                <thead><tr><th>Variable</th><th>Nilai Default</th><th>Kegunaan</th></tr></thead>
                <tbody>
                  <tr><td class="text-mono cell-primary">--bg-base</td><td class="text-mono">#0B0E1A</td><td>Warna latar halaman</td></tr>
                  <tr><td class="text-mono cell-primary">--bg-surface</td><td class="text-mono">#13172B</td><td>Warna dasar card &amp; sidebar</td></tr>
                  <tr><td class="text-mono cell-primary">--accent-1 / --accent-2</td><td class="text-mono">#6366F1 / #8B5CF6</td><td>Gradient aksen utama (tombol, highlight)</td></tr>
                  <tr><td class="text-mono cell-primary">--font-display</td><td class="text-mono">Space Grotesk</td><td>Font judul/heading</td></tr>
                  <tr><td class="text-mono cell-primary">--font-mono</td><td class="text-mono">JetBrains Mono</td><td>Font angka statistik &amp; kode</td></tr>
                  <tr><td class="text-mono cell-primary">--radius-md</td><td class="text-mono">12px</td><td>Radius standar untuk card &amp; tombol</td></tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- LAYOUT: SIDEBAR -->
          <section class="doc-section" id="layout-sidebar">
            <h2>Sidebar</h2>
            <p class="lead-desc">Sidebar mendukung mode lipat (collapse) di desktop dan mode drawer di perangkat mobile. State collapse disimpan otomatis di <code>localStorage</code>. Ketika dilipat, hover pada item akan menampilkan flyout interaktif yang bisa diklik untuk membuka halaman terkait (submenu ditampilkan sebagai link biasa saat sidebar terlipat).</p>
            <div class="doc-sub">
              <h4>Struktur dasar</h4>
              <p class="sub-desc">Gunakan <code>.nav-item.active</code> untuk menandai menu yang sedang aktif, dan <code>data-submenu-toggle</code> untuk membuat submenu accordion.</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeSidebar"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeSidebar"><code class="language-html">&lt;aside class="sidebar" id="appSidebar"&gt;
  &lt;div class="sidebar-brand"&gt;...&lt;/div&gt;
  &lt;nav class="sidebar-nav"&gt;
    &lt;div class="nav-section-title"&gt;Overview&lt;/div&gt;
    &lt;ul class="list-unstyled"&gt;
      &lt;li class="nav-item active"&gt;
        &lt;a href="index.html" class="nav-link"&gt;
          &lt;i class="bi bi-grid-1x2-fill"&gt;&lt;/i&gt;
          &lt;span class="nav-label-text"&gt;Dashboard&lt;/span&gt;
        &lt;/a&gt;
      &lt;/li&gt;
      &lt;li class="nav-item"&gt;
        &lt;a href="#" class="nav-link" data-submenu-toggle&gt;
          &lt;i class="bi bi-chat-dots-fill"&gt;&lt;/i&gt;
          &lt;span class="nav-label-text"&gt;Conversations&lt;/span&gt;
        &lt;/a&gt;
        &lt;ul class="nav-submenu" style="display:none;"&gt;
          &lt;li class="nav-item"&gt;&lt;a href="#" class="nav-link"&gt;All Chats&lt;/a&gt;&lt;/li&gt;
        &lt;/ul&gt;
      &lt;/li&gt;
    &lt;/ul&gt;
  &lt;/nav&gt;
&lt;/aside&gt;</code></pre>
              </div>
            </div>
            <div class="doc-sub">
              <h4>Tombol kontrol sidebar</h4>
              <p class="sub-desc">Dua tombol disediakan: <code>#sidebarCollapseBtn</code> untuk melipat di desktop, dan <code>#sidebarMobileToggle</code> untuk membuka drawer di mobile. Logikanya sudah otomatis ditangani oleh <code>index.js</code>.</p>
            </div>
          </section>

          <!-- LAYOUT: TOPBAR -->
          <section class="doc-section" id="layout-topbar">
            <h2>Topbar</h2>
            <p class="lead-desc">Topbar berisi pencarian global, toggle tema, notifikasi, dan dropdown profil pengguna. Topbar bersifat <code>sticky</code> di bagian atas saat halaman discroll.</p>
            <div class="doc-preview">
              <button class="icon-btn"><i class="bi bi-sun"></i></button>
              <button class="icon-btn"><i class="bi bi-bell-fill"></i><span class="dot-badge"></span></button>
              <div class="user-chip"><div class="user-avatar">RA</div><div><div class="user-chip-name">Rangga A.</div><div class="user-chip-role">Administrator</div></div></div>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeTopbar"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeTopbar"><code class="language-html">&lt;button class="icon-btn" id="themeToggleBtn"&gt;
  &lt;i class="bi bi-sun"&gt;&lt;/i&gt;
&lt;/button&gt;

&lt;button class="icon-btn"&gt;
  &lt;i class="bi bi-bell-fill"&gt;&lt;/i&gt;
  &lt;span class="dot-badge"&gt;&lt;/span&gt;
&lt;/button&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: BUTTONS -->
          <section class="doc-section" id="comp-buttons">
            <h2>Buttons</h2>
            <p class="lead-desc">Empat varian tombol utama: gradient primer, outline, ghost, dan icon-square.</p>
            <div class="doc-preview">
              <button class="btn btn-primary-grad">Primary</button>
              <button class="btn btn-outline-soft">Outline</button>
              <button class="btn btn-ghost">Ghost</button>
              <button class="btn btn-outline-soft btn-icon-sq"><i class="bi bi-three-dots"></i></button>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeButtons"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeButtons"><code class="language-html">&lt;button class="btn btn-primary-grad"&gt;Primary&lt;/button&gt;
&lt;button class="btn btn-outline-soft"&gt;Outline&lt;/button&gt;
&lt;button class="btn btn-ghost"&gt;Ghost&lt;/button&gt;
&lt;button class="btn btn-outline-soft btn-icon-sq"&gt;
  &lt;i class="bi bi-three-dots"&gt;&lt;/i&gt;
&lt;/button&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: CARDS -->
          <section class="doc-section" id="comp-cards">
            <h2>Cards</h2>
            <p class="lead-desc">Gunakan <code>.card-glow</code> untuk efek border gradient saat hover (signature visual template ini), atau <code>.card</code> saja untuk card statis.</p>
            <div class="doc-preview">
              <div class="card card-glow" style="width:220px;"><div class="card-inner card-body">
                <h6 class="mb-1">Card Glow</h6>
                <p class="text-muted-c mb-0" style="font-size:0.8rem;">Hover di sini</p>
              </div></div>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeCards"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeCards"><code class="language-html">&lt;div class="card card-glow"&gt;
  &lt;div class="card-inner card-body"&gt;
    &lt;h6&gt;Judul Card&lt;/h6&gt;
    &lt;p&gt;Konten card di sini.&lt;/p&gt;
  &lt;/div&gt;
&lt;/div&gt;

&lt;!-- Card statis (tanpa efek hover) --&gt;
&lt;div class="card"&gt;
  &lt;div class="card-body"&gt;...&lt;/div&gt;
&lt;/div&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: PILLS -->
          <section class="doc-section" id="comp-pills">
            <h2>Pills &amp; Badge</h2>
            <p class="lead-desc">Indikator status berwarna untuk tabel, kartu, atau daftar. Tersedia 5 varian warna semantik.</p>
            <div class="doc-preview">
              <span class="pill pill-success">Sukses</span>
              <span class="pill pill-danger">Gagal</span>
              <span class="pill pill-warning">Pending</span>
              <span class="pill pill-info">Info</span>
              <span class="pill pill-neutral">Netral</span>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codePills"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codePills"><code class="language-html">&lt;span class="pill pill-success"&gt;Sukses&lt;/span&gt;
&lt;span class="pill pill-danger"&gt;Gagal&lt;/span&gt;
&lt;span class="pill pill-warning"&gt;Pending&lt;/span&gt;
&lt;span class="pill pill-info"&gt;Info&lt;/span&gt;
&lt;span class="pill pill-neutral"&gt;Netral&lt;/span&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: TABLE -->
          <section class="doc-section" id="comp-table">
            <h2>Table</h2>
            <p class="lead-desc">Gunakan class <code>.table-modern</code> pada elemen <code>&lt;table&gt;</code> standar. Tidak memerlukan JavaScript tambahan untuk tampilannya.</p>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeTable"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeTable"><code class="language-html">&lt;table class="table-modern"&gt;
  &lt;thead&gt;
    &lt;tr&gt;&lt;th&gt;Nama&lt;/th&gt;&lt;th&gt;Status&lt;/th&gt;&lt;/tr&gt;
  &lt;/thead&gt;
  &lt;tbody&gt;
    &lt;tr&gt;
      &lt;td class="cell-primary"&gt;Sinta Amalia&lt;/td&gt;
      &lt;td&gt;&lt;span class="pill pill-success"&gt;Aktif&lt;/span&gt;&lt;/td&gt;
    &lt;/tr&gt;
  &lt;/tbody&gt;
&lt;/table&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: FORMS -->
          <section class="doc-section" id="comp-forms">
            <h2>Form Elements</h2>
            <p class="lead-desc">Semua elemen form (input, select, textarea, switch, checkbox) menggunakan prefix class <code>-modern</code> agar tidak konflik dengan class Bootstrap default.</p>
            <div class="doc-preview" style="flex-direction:column; align-items:flex-start;">
              <input type="text" class="form-control-modern" placeholder="Input teks..." style="max-width:280px;">
              <label class="switch-modern mt-2"><input type="checkbox" checked><span class="switch-track"></span></label>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeForms"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeForms"><code class="language-html">&lt;label class="form-label-modern"&gt;Email&lt;/label&gt;
&lt;input type="email" class="form-control-modern" placeholder="nama@email.com"&gt;

&lt;select class="form-select-modern"&gt;
  &lt;option&gt;Administrator&lt;/option&gt;
&lt;/select&gt;

&lt;!-- Toggle switch --&gt;
&lt;label class="switch-modern"&gt;
  &lt;input type="checkbox" checked&gt;
  &lt;span class="switch-track"&gt;&lt;/span&gt;
&lt;/label&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: TABS -->
          <section class="doc-section" id="comp-tabs">
            <h2>Tabs</h2>
            <p class="lead-desc">Tabs sederhana berbasis atribut <code>data-tab-target</code> dan <code>data-tab-panel</code>, ditangani otomatis oleh <code>index.js</code> tanpa dependensi tambahan.</p>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeTabs"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeTabs"><code class="language-html">&lt;div class="tabs-modern"&gt;
  &lt;span class="tab-link active" data-tab-target="#panelA"&gt;Detail&lt;/span&gt;
  &lt;span class="tab-link" data-tab-target="#panelB"&gt;Riwayat&lt;/span&gt;
&lt;/div&gt;
&lt;div id="panelA" data-tab-panel style="display:block;"&gt;Konten A&lt;/div&gt;
&lt;div id="panelB" data-tab-panel style="display:none;"&gt;Konten B&lt;/div&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: MODAL -->
          <section class="doc-section" id="comp-modal">
            <h2>Modal</h2>
            <p class="lead-desc">Modal menggunakan komponen Bootstrap 5 standar (<code>data-bs-toggle="modal"</code>) dengan override visual gelap di <code>main.css</code>.</p>
            <div class="doc-preview">
              <button class="btn btn-primary-grad" data-bs-toggle="modal" data-bs-target="#docDemoModal">Buka Contoh Modal</button>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeModal"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeModal"><code class="language-html">&lt;button data-bs-toggle="modal" data-bs-target="#myModal"&gt;Buka&lt;/button&gt;

&lt;div class="modal fade" id="myModal"&gt;
  &lt;div class="modal-dialog"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;...&lt;/div&gt;
      &lt;div class="modal-body"&gt;...&lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
            </div>
          </section>

          <!-- COMP: TOAST -->
          <section class="doc-section" id="comp-toast">
            <h2>Toast</h2>
            <p class="lead-desc">Panggil fungsi global <code>NexoraToast(pesan, tipe)</code> dari JavaScript di mana saja. Tipe yang didukung: <code>success</code>, <code>danger</code>, dan <code>default</code>.</p>
            <div class="doc-preview">
              <button class="btn btn-outline-soft" onclick="NexoraToast('Ini contoh notifikasi sukses.', 'success')">Coba Toast</button>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeToast"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeToast"><code class="language-javascript">// Panggil di mana saja setelah index.js dimuat
NexoraToast('Perubahan berhasil disimpan.', 'success');
NexoraToast('Terjadi kesalahan.', 'danger');
NexoraToast('Informasi umum.', 'default');</code></pre>
            </div>
          </section>

          <!-- COMP: CHART -->
          <section class="doc-section" id="comp-chart">
            <h2>Charts</h2>
            <p class="lead-desc">Grafik menggunakan <a href="https://www.chartjs.org/" target="_blank" rel="noopener" style="color:var(--accent-cyan);">Chart.js</a> via CDN. Dashboard utama menggunakan 3 chart dari <code>index.js</code>, sedangkan halaman <code>charts.html</code> menampilkan <strong>10 jenis chart modern</strong> dari file modular <code>assets/js/charts.js</code>.</p>

            <div class="doc-sub">
              <h4>Chart di Dashboard (<code>index.html</code>)</h4>
              <p class="sub-desc">Tiga chart otomatis dari <code>index.js</code> — line revenue, doughnut usage, bar activity.</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeChartsBase"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeChartsBase"><code class="language-html">&lt;canvas id="chartRevenue"&gt;&lt;/canvas&gt;  &lt;!-- Line chart --&gt;
&lt;canvas id="chartUsage"&gt;&lt;/canvas&gt;    &lt;!-- Doughnut chart --&gt;
&lt;canvas id="chartBar"&gt;&lt;/canvas&gt;      &lt;!-- Bar chart --&gt;</code></pre>
              </div>
            </div>

            <div class="doc-sub">
              <h4>Chart di Halaman Charts (<code>charts.html</code>)</h4>
              <p class="sub-desc">Halaman khusus chart menggunakan file <code>assets/js/charts.js</code> dengan 10 tipe chart modern. Cukup tambahkan <code>&lt;canvas id="..."&gt;</code> di HTML dan file JS akan merendernya otomatis.</p>

              <div class="table-responsive mb-3">
                <table class="table-modern doc-table">
                  <thead><tr><th>Canvas ID</th><th>Tipe Chart</th><th>Deskripsi</th></tr></thead>
                  <tbody>
                    <tr><td class="text-mono cell-primary">#chartRevenueLine</td><td>Line</td><td>Tren pendapatan 12 bulan dengan area gradient &amp; garis proyeksi dashed</td></tr>
                    <tr><td class="text-mono cell-primary">#chartAIModels</td><td>Doughnut</td><td>Proporsi penggunaan model AI dengan cutout 70%</td></tr>
                    <tr><td class="text-mono cell-primary">#chartDailyRequests</td><td>Bar</td><td>Request per hari (7 hari) — bar vertikal dengan warna berbeda</td></tr>
                    <tr><td class="text-mono cell-primary">#chartTeamPerformance</td><td>Horizontal Bar</td><td>Task selesai per anggota tim — bar horizontal dengan <code>indexAxis: 'y'</code></td></tr>
                    <tr><td class="text-mono cell-primary">#chartStackedCosts</td><td>Stacked Bar</td><td>Breakdown biaya per kuartal dengan 3 dataset bertumpuk</td></tr>
                    <tr><td class="text-mono cell-primary">#chartTrafficSources</td><td>Polar Area</td><td>Sumber trafik berdasarkan channel akuisisi</td></tr>
                    <tr><td class="text-mono cell-primary">#chartProductRadar</td><td>Radar</td><td>Perbandingan skor produk pada 6 dimensi (UX, Performance, dll)</td></tr>
                    <tr><td class="text-mono cell-primary">#chartMixedRevenue</td><td>Mixed (Bar + Line)</td><td>Orders (bar) + Revenue (line) dalam satu canvas</td></tr>
                    <tr><td class="text-mono cell-primary">#chartPortfolio</td><td>Bubble</td><td>Analisis portofolio — Risk vs Return, ukuran bubble = alokasi</td></tr>
                    <tr><td class="text-mono cell-primary">#chartScatter</td><td>Scatter</td><td>Korelasi antara pengguna aktif dan engagement rate</td></tr>
                  </tbody>
                </table>
              </div>

              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeChartsFull"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeChartsFull"><code class="language-html">&lt;!-- Di dalam card --&gt;
&lt;div class="card"&gt;
  &lt;div class="card-body" style="height:280px;"&gt;
    &lt;canvas id="chartRevenueLine"&gt;&lt;/canvas&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
              </div>
            </div>

            <div class="doc-sub">
              <h4>Menambahkan Chart Baru</h4>
              <p class="sub-desc">Untuk menambahkan chart kustom, buka <code>assets/js/charts.js</code> dan ikuti pola yang sudah ada. Setiap chart adalah blok <code>if (document.getElementById("..."))</code> sehingga aman ditambahkan tanpa merusak chart lain. Gunakan palet warna dari objek <code>colors</code> yang sudah didefinisikan di awal file.</p>
            </div>
          </section>

          <!-- ALERTS -->
          <section class="doc-section" id="comp-alerts">
            <h2>Alerts</h2>
            <p class="lead-desc">Notifikasi dalam halaman (inline) dengan 4 varian semantik: success, danger, warning, info. Mendukung judul, ikon, dan tombol tutup.</p>
            <div class="doc-preview" style="flex-direction:column; align-items:stretch;">
              <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i><div class="alert-content"><div class="alert-title">Berhasil</div>Data berhasil disimpan.</div></div>
              <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i><div class="alert-content">Gagal memuat data.</div><button class="alert-close">&times;</button></div>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeAlerts"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeAlerts"><code class="language-html">&lt;div class="alert alert-success"&gt;
  &lt;i class="bi bi-check-circle-fill"&gt;&lt;/i&gt;
  &lt;div class="alert-content"&gt;
    &lt;div class="alert-title"&gt;Berhasil&lt;/div&gt;
    Pesan notifikasi di sini.
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
            </div>
          </section>

          <!-- AVATARS -->
          <section class="doc-section" id="comp-avatars">
            <h2>Avatars</h2>
            <p class="lead-desc">Avatar dengan 5 ukuran (xs, sm, md, lg, xl), avatar group dengan stacking, dan indikator status (online, busy, away).</p>
            <div class="doc-preview">
              <span class="avatar avatar-sm">RA</span>
              <span class="avatar avatar-md">RA</span>
              <span class="avatar avatar-lg">RA</span>
              <div class="avatar-group">
                <span class="avatar avatar-sm" style="background:#6366F1;">AD</span>
                <span class="avatar avatar-sm" style="background:#34D399;">MS</span>
                <span class="avatar-more">+3</span>
              </div>
              <span class="avatar avatar-md">RA<span class="avatar-status online"></span></span>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeAvatars"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeAvatars"><code class="language-html">&lt;!-- Ukuran --&gt;
&lt;span class="avatar avatar-xs"&gt;RA&lt;/span&gt;
&lt;span class="avatar avatar-sm"&gt;RA&lt;/span&gt;
&lt;span class="avatar avatar-lg"&gt;RA&lt;/span&gt;

&lt;!-- Group --&gt;
&lt;div class="avatar-group"&gt;
  &lt;span class="avatar avatar-sm"&gt;AD&lt;/span&gt;
  &lt;span class="avatar avatar-sm"&gt;MS&lt;/span&gt;
  &lt;span class="avatar-more"&gt;+3&lt;/span&gt;
&lt;/div&gt;</code></pre>
            </div>
          </section>

          <!-- TIMELINE -->
          <section class="doc-section" id="comp-timeline">
            <h2>Timeline</h2>
            <p class="lead-desc">Feed aktivitas vertikal dengan dot indikator (default, success, danger, warning). Cocok untuk log aktivitas, riwayat order, atau event stream.</p>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeTimeline"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeTimeline"><code class="language-html">&lt;ul class="timeline"&gt;
  &lt;li class="timeline-item"&gt;
    &lt;span class="timeline-dot"&gt;&lt;i class="bi bi-circle-fill"&gt;&lt;/i&gt;&lt;/span&gt;
    &lt;div class="timeline-content"&gt;
      &lt;h6&gt;Judul Event&lt;/h6&gt;
      &lt;p&gt;Deskripsi event.&lt;/p&gt;
      &lt;span class="timeline-time"&gt;18 Jun 2026, 14:23&lt;/span&gt;
    &lt;/div&gt;
  &lt;/li&gt;
&lt;/ul&gt;</code></pre>
            </div>
          </section>

          <!-- PAGINATION -->
          <section class="doc-section" id="comp-pagination">
            <h2>Pagination</h2>
            <p class="lead-desc">Navigasi halaman dengan class <code>.pagination-modern</code>. Item aktif mendapat gradient aksen, item disabled terlihat redup.</p>
            <div class="doc-preview">
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
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codePagination"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codePagination"><code class="language-html">&lt;ul class="pagination-modern"&gt;
  &lt;li class="disabled"&gt;&lt;span&gt;&amp;laquo;&lt;/span&gt;&lt;/li&gt;
  &lt;li class="active"&gt;&lt;a href="#"&gt;1&lt;/a&gt;&lt;/li&gt;
  &lt;li&gt;&lt;a href="#"&gt;3&lt;/a&gt;&lt;/li&gt;
  &lt;li&gt;&lt;span&gt;...&lt;/span&gt;&lt;/li&gt;
  &lt;li&gt;&lt;a href="#"&gt;&amp;raquo;&lt;/a&gt;&lt;/li&gt;
&lt;/ul&gt;</code></pre>
            </div>
          </section>

          <!-- SPINNERS -->
          <section class="doc-section" id="comp-spinners">
            <h2>Spinners &amp; Skeleton</h2>
            <p class="lead-desc">Dua jenis spinner (border ring dan dots) dengan 3 ukuran, skeleton loader untuk placeholder konten, dan loading overlay untuk card.</p>
            <div class="doc-preview">
              <div class="spinner"></div>
              <div class="spinner spinner-sm"></div>
              <div class="spinner spinner-lg"></div>
              <div class="spinner-dots"><span></span><span></span><span></span></div>
            </div>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeSpinners"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeSpinners"><code class="language-html">&lt;!-- Spinner ring --&gt;
&lt;div class="spinner"&gt;&lt;/div&gt;
&lt;div class="spinner spinner-sm"&gt;&lt;/div&gt;
&lt;div class="spinner spinner-lg"&gt;&lt;/div&gt;

&lt;!-- Skeleton --&gt;
&lt;div class="skeleton skeleton-heading"&gt;&lt;/div&gt;
&lt;div class="skeleton skeleton-text"&gt;&lt;/div&gt;</code></pre>
            </div>
          </section>

          <!-- ACCORDION -->
          <section class="doc-section" id="comp-accordion">
            <h2>Accordion</h2>
            <p class="lead-desc">Accordion sederhana dengan atribut <code>data-accordion-toggle</code>. Saat satu item dibuka, item lain tertutup otomatis. Didukung ikon di header.</p>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeAccordion"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeAccordion"><code class="language-html">&lt;div class="accordion-modern"&gt;
  &lt;div class="accordion-item"&gt;
    &lt;div class="accordion-header open" data-accordion-toggle&gt;
      &lt;i class="bi bi-envelope-fill"&gt;&lt;/i&gt;
      Pertanyaan?
      &lt;i class="bi bi-chevron-down"&gt;&lt;/i&gt;
    &lt;/div&gt;
    &lt;div class="accordion-body open"&gt;
      Jawaban di sini.
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
            </div>
          </section>

          <!-- DROPZONE & TAGS -->
          <section class="doc-section" id="comp-dropzone">
            <h2>Dropzone &amp; Tags</h2>
            <p class="lead-desc">Area unggah file dengan drag-drop support, dan tag/chip dengan tombol hapus (<code>.tag i.bi-x</code>).</p>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeTags"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeTags"><code class="language-html">&lt;!-- Dropzone --&gt;
&lt;div class="dropzone"&gt;
  &lt;i class="bi bi-cloud-arrow-up-fill"&gt;&lt;/i&gt;
  &lt;h6&gt;Seret file ke sini&lt;/h6&gt;
  &lt;p&gt;Mendukung PNG, JPG, PDF&lt;/p&gt;
&lt;/div&gt;

&lt;!-- Tags --&gt;
&lt;span class="tag"&gt;frontend &lt;i class="bi bi-x"&gt;&lt;/i&gt;&lt;/span&gt;
&lt;span class="tag tag-danger"&gt;urgent &lt;i class="bi bi-x"&gt;&lt;/i&gt;&lt;/span&gt;</code></pre>
            </div>
          </section>

          <!-- LIST GROUP -->
          <section class="doc-section" id="comp-listgroup">
            <h2>List Group</h2>
            <p class="lead-desc">Daftar dengan border, icon, dan teks primer/sekunder. Item <code>.active</code> mendapat highlight biru dengan border kiri.</p>
            <div class="doc-code-wrap">
              <button class="doc-copy-btn" data-copy-target="#codeListGroup"><i class="bi bi-clipboard"></i> Copy</button>
              <pre id="codeListGroup"><code class="language-html">&lt;ul class="list-group-modern"&gt;
  &lt;li class="active"&gt;
    &lt;span class="list-group-icon"&gt;&lt;i class="bi bi-person-fill"&gt;&lt;/i&gt;&lt;/span&gt;
    &lt;div&gt;
      &lt;div class="list-group-primary"&gt;Profil Saya&lt;/div&gt;
      &lt;div class="list-group-secondary"&gt;Pengaturan akun&lt;/div&gt;
    &lt;/div&gt;
  &lt;/li&gt;
&lt;/ul&gt;</code></pre>
            </div>
          </section>

          <!-- MISC -->
          <section class="doc-section" id="comp-misc">
            <h2>Callout, Steps &amp; Rating</h2>

            <div class="doc-sub">
              <h4>Callout</h4>
              <p class="sub-desc">Kotak informasi dengan border kiri berwarna. Tersedia 4 varian: <code>.callout-info</code>, <code>.callout-success</code>, <code>.callout-danger</code>, <code>.callout-warning</code>.</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeCallout"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeCallout"><code class="language-html">&lt;div class="callout callout-info"&gt;
  &lt;div class="callout-title"&gt;
    &lt;i class="bi bi-info-circle-fill"&gt;&lt;/i&gt; Informasi
  &lt;/div&gt;
  &lt;div class="callout-body"&gt;Isi pesan di sini.&lt;/div&gt;
&lt;/div&gt;</code></pre>
              </div>
            </div>

            <div class="doc-sub">
              <h4>Steps / Stepper</h4>
              <p class="sub-desc">Progress multi-langkah untuk wizard, form beruntun, atau checkout. Gunakan <code>.step-item.completed</code>, <code>.active</code>, atau default.</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeSteps"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeSteps"><code class="language-html">&lt;div class="steps-modern"&gt;
  &lt;div class="step-item completed"&gt;
    &lt;div class="step-number"&gt;&lt;i class="bi bi-check"&gt;&lt;/i&gt;&lt;/div&gt;
    &lt;div class="step-label"&gt;Akun&lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="step-item active"&gt;
    &lt;div class="step-number"&gt;2&lt;/div&gt;
    &lt;div class="step-label"&gt;Profil&lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
              </div>
            </div>

            <div class="doc-sub">
              <h4>Rating</h4>
              <p class="sub-desc">Rating interaktif (input radio) dan statis (ikon).</p>
              <div class="doc-code-wrap">
                <button class="doc-copy-btn" data-copy-target="#codeRating"><i class="bi bi-clipboard"></i> Copy</button>
                <pre id="codeRating"><code class="language-html">&lt;!-- Rating statis --&gt;
&lt;div class="rating-static"&gt;
  &lt;i class="bi bi-star-fill filled"&gt;&lt;/i&gt;
  &lt;i class="bi bi-star-fill filled"&gt;&lt;/i&gt;
  &lt;i class="bi bi-star-fill filled"&gt;&lt;/i&gt;
  &lt;i class="bi bi-star-fill"&gt;&lt;/i&gt;
  &lt;i class="bi bi-star"&gt;&lt;/i&gt;
&lt;/div&gt;</code></pre>
              </div>
            </div>
          </section>

          <!-- JS API -->
          <section class="doc-section" id="js-api">
            <h2>JavaScript API</h2>
            <p class="lead-desc">Daftar fungsi dan event yang tersedia secara global dari <code>assets/js/index.js</code>.</p>
            <div class="table-responsive">
              <table class="table-modern doc-table">
                <thead><tr><th>Fungsi / Selector</th><th>Deskripsi</th></tr></thead>
                <tbody>
                  <tr><td class="text-mono cell-primary">NexoraToast(msg, type)</td><td>Menampilkan notifikasi toast. <code>type</code>: success / danger / default.</td></tr>
                  <tr><td class="text-mono cell-primary">#sidebarCollapseBtn</td><td>Tombol untuk melipat/membuka sidebar di desktop.</td></tr>
                  <tr><td class="text-mono cell-primary">#themeToggleBtn</td><td>Tombol untuk beralih antara mode gelap dan terang.</td></tr>
                  <tr><td class="text-mono cell-primary">[data-counter="angka"]</td><td>Elemen akan otomatis dianimasikan dari 0 ke nilai target saat halaman dimuat.</td></tr>
                  <tr><td class="text-mono cell-primary">[data-copy-target="#id"]</td><td>Tombol salin yang akan menyalin konten elemen target ke clipboard.</td></tr>
                  <tr><td class="text-mono cell-primary">[data-submenu-toggle]</td><td>Menjadikan nav-item sebagai accordion yang dapat membuka submenu.</td></tr>
                  <tr><td class="text-mono cell-primary">[data-accordion-toggle]</td><td>Header accordion — klik untuk buka/tutup panel body.</td></tr>
                  <tr><td class="text-mono cell-primary">.alert .alert-close</td><td>Tombol tutup pada alert — klik untuk menyembunyikan alert dengan animasi.</td></tr>
                  <tr><td class="text-mono cell-primary">.tag i.bi-x</td><td>Ikon hapus pada tag — klik untuk menghapus tag dari tampilan.</td></tr>
                  <tr><td class="text-mono cell-primary">.dropzone</td><td>Area unggah dengan drag-drop visual. Klik untuk membuka dialog (simulasi).</td></tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- FAQ -->
          <section class="doc-section" id="faq">
            <h2>FAQ</h2>
            <div class="doc-sub">
              <h4>Apakah template ini butuh proses build (Webpack/Vite)?</h4>
              <p class="sub-desc">Tidak. Semua file adalah HTML/CSS/JS statis dan siap pakai langsung.</p>
            </div>
            <div class="doc-sub">
              <h4>Bagaimana cara menambahkan halaman baru?</h4>
              <p class="sub-desc">Duplikat salah satu file halaman yang sudah ada (misalnya <code>tables.html</code>), lalu ubah konten di dalam <code>&lt;main class="page-content"&gt;</code> sesuai kebutuhan. Struktur sidebar dan topbar bisa disalin apa adanya agar navigasi tetap konsisten.</p>
            </div>
            <div class="doc-sub">
              <h4>Bisakah saya mengganti font?</h4>
              <p class="sub-desc">Ya. Ubah baris <code>@import</code> Google Fonts di bagian atas <code>main.css</code>, lalu sesuaikan nilai variable <code>--font-display</code>, <code>--font-body</code>, dan <code>--font-mono</code> di <code>:root</code>.</p>
            </div>
          </section>

        </div>
      </div>

      <!-- Modal demo untuk dokumentasi -->
      <div class="modal fade" id="docDemoModal" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Contoh Modal</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"><p class="text-secondary-c mb-0" style="font-size:0.88rem;">Ini adalah isi modal demonstrasi untuk dokumentasi.</p></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-bash.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-markup.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js"></script>
<script>
  // Scrollspy sederhana untuk TOC dokumentasi
  document.addEventListener('DOMContentLoaded', function () {
    const sections = document.querySelectorAll('.doc-section');
    const tocLinks = document.querySelectorAll('#docToc a');
    function onScroll() {
      let current = sections[0];
      sections.forEach(function (sec) {
        if (window.scrollY + 100 >= sec.offsetTop) current = sec;
      });
      tocLinks.forEach(function (link) {
        link.classList.toggle('active', link.getAttribute('href') === '#' + current.id);
      });
    }
    window.addEventListener('scroll', onScroll);
    onScroll();
  });
</script>
@endpush
