@extends('docs.layouts.app')

@section('title', 'Tables')

@php $activeMenu = 'tables' @endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css">
@endpush

@section('content')
      <div class="page-header">
        <div>
          <h1>Tables</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Tables</span></div>
        </div>
        <button class="btn btn-primary-grad"><i class="bi bi-plus-lg me-1"></i>Tambah Data</button>
      </div>

      <!-- ── TABLE 1: BASIC / STRIPED ── -->
      <div class="card mb-3">
        <div class="card-header-flex">
          <h6>Tabel Stripe — Daftar Pesanan</h6>
          <span class="chip-tag">striped</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table-modern striped">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Pelanggan</th>
                  <th>Produk</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                </tr>
              </thead>
              <tbody>
                <tr><td class="text-mono">#INV-001</td><td class="cell-primary">Aditya Wijaya</td><td>Paket Pro</td><td class="text-mono">$49.00</td><td><span class="pill pill-success">Sukses</span></td><td class="text-muted-c">18 Jun 2026</td></tr>
                <tr><td class="text-mono">#INV-002</td><td class="cell-primary">Melati Sari</td><td>Paket Team</td><td class="text-mono">$129.00</td><td><span class="pill pill-warning">Pending</span></td><td class="text-muted-c">17 Jun 2026</td></tr>
                <tr><td class="text-mono">#INV-003</td><td class="cell-primary">Randy Putra</td><td>Paket Basic</td><td class="text-mono">$19.00</td><td><span class="pill pill-danger">Gagal</span></td><td class="text-muted-c">16 Jun 2026</td></tr>
                <tr><td class="text-mono">#INV-004</td><td class="cell-primary">Dewi Lestari</td><td>Paket Pro</td><td class="text-mono">$49.00</td><td><span class="pill pill-success">Sukses</span></td><td class="text-muted-c">15 Jun 2026</td></tr>
                <tr><td class="text-mono">#INV-005</td><td class="cell-primary">Fitri Handayani</td><td>Paket Enterprise</td><td class="text-mono">$299.00</td><td><span class="pill pill-info">Proses</span></td><td class="text-muted-c">14 Jun 2026</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── TABLE 2: BORDERED / COMPACT ── -->
      <div class="mb-3">
        <div class="row g-3">
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-header-flex">
                <h6>Tabel Compact — Log Aktivitas</h6>
                <span class="chip-tag">compact</span>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table-modern" style="font-size:0.78rem;">
                    <thead>
                      <tr>
                        <th>Waktu</th>
                        <th>Aksi</th>
                        <th>Pengguna</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr><td class="text-mono">14:23:12</td><td>Login berhasil</td><td class="cell-primary">aditya@nexora.id</td></tr>
                      <tr><td class="text-mono">14:20:05</td><td>Update profil</td><td class="cell-primary">melati@nexora.id</td></tr>
                      <tr><td class="text-mono">14:15:44</td><td>Export laporan</td><td class="cell-primary">randy@nexora.id</td></tr>
                      <tr><td class="text-mono">14:10:30</td><td>Hapus file</td><td class="cell-primary">dewi@nexora.id</td></tr>
                      <tr><td class="text-mono">14:05:18</td><td>Tambah user</td><td class="cell-primary">fitri@nexora.id</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card h-100">
              <div class="card-header-flex">
                <h6>Tabel dengan Ringkasan (Footer)</h6>
                <span class="chip-tag">footer</span>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table-modern">
                    <thead>
                      <tr><th>Item</th><th>Qty</th><th style="text-align:right;">Harga</th><th style="text-align:right;">Subtotal</th></tr>
                    </thead>
                    <tbody>
                      <tr><td class="cell-primary">Paket Pro — Langganan Bulanan</td><td>2</td><td class="text-mono" style="text-align:right;">$49.00</td><td class="text-mono" style="text-align:right;">$98.00</td></tr>
                      <tr><td class="cell-primary">Domain Kustom</td><td>1</td><td class="text-mono" style="text-align:right;">$15.00</td><td class="text-mono" style="text-align:right;">$15.00</td></tr>
                      <tr><td class="cell-primary">Storage Tambahan 50GB</td><td>3</td><td class="text-mono" style="text-align:right;">$10.00</td><td class="text-mono" style="text-align:right;">$30.00</td></tr>
                    </tbody>
                    <tfoot>
                      <tr><td colspan="3">Total</td><td class="text-mono" style="text-align:right;">$143.00</td></tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── TABLE 3: CARD TABLE ── -->
      <div class="card mb-3">
        <div class="card-header-flex">
          <h6>Card Table — Tim Proyek</h6>
          <span class="chip-tag">card-style</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table-modern">
              <thead>
                <tr><th>Anggota</th><th>Role</th><th>Departemen</th><th>Kontribusi</th><th>Aksi</th></tr>
              </thead>
              <tbody>
                <tr>
                  <td class="cell-primary"><span class="avatar-sm" style="background:#6366F1;">SA</span>Sinta Amalia</td>
                  <td><span class="badge badge-primary">Frontend Lead</span></td>
                  <td>Engineering</td>
                  <td><div class="progress-modern" style="width:120px; height:6px;"><div class="bar" style="width:92%;"></div></div></td>
                  <td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-three-dots"></i></button></td>
                </tr>
                <tr>
                  <td class="cell-primary"><span class="avatar-sm" style="background:#34D399;">BP</span>Budi Pratama</td>
                  <td><span class="badge badge-info">Backend Dev</span></td>
                  <td>Engineering</td>
                  <td><div class="progress-modern" style="width:120px; height:6px;"><div class="bar" style="width:78%;"></div></div></td>
                  <td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-three-dots"></i></button></td>
                </tr>
                <tr>
                  <td class="cell-primary"><span class="avatar-sm" style="background:#F87171;">DK</span>Dewi Kartika</td>
                  <td><span class="badge badge-success">UI Designer</span></td>
                  <td>Design</td>
                  <td><div class="progress-modern" style="width:120px; height:6px;"><div class="bar" style="width:65%;"></div></div></td>
                  <td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-three-dots"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ── TABLE 4: FULL DATA TABLE (simple-datatables) ── -->
      <div class="card mb-3">
        <div class="card-header-flex">
          <div>
            <h6>DataTable Lengkap — Manajemen Pengguna</h6>
            <span class="text-muted-c" style="font-size:0.78rem;">Filter, search, sorting &amp; pagination built-in</span>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <select class="form-select-modern" id="dtStatusFilter" style="width:auto; min-width:130px;">
              <option value="">Semua Status</option>
              <option value="Aktif">Aktif</option>
              <option value="Pending">Pending</option>
              <option value="Nonaktif">Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table id="usersDataTable" class="table-modern">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Peran</th>
                  <th>Tim</th>
                  <th>Status</th>
                  <th>Bergabung</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr><td>Aditya Wijaya</td><td>aditya@nexora.id</td><td><span class="chip-tag">Admin</span></td><td>Platform</td><td><span class="pill pill-success">Aktif</span></td><td class="text-muted-c">05 Jan 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Melati Sari</td><td>melati@nexora.id</td><td><span class="chip-tag">Editor</span></td><td>Konten</td><td><span class="pill pill-warning">Pending</span></td><td class="text-muted-c">18 Feb 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Randy Putra</td><td>randy@nexora.id</td><td><span class="chip-tag">Viewer</span></td><td>Support</td><td><span class="pill pill-neutral">Nonaktif</span></td><td class="text-muted-c">01 Mar 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Dewi Lestari</td><td>dewi@nexora.id</td><td><span class="chip-tag">Editor</span></td><td>Design</td><td><span class="pill pill-success">Aktif</span></td><td class="text-muted-c">22 Mar 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Fitri Handayani</td><td>fitri@nexora.id</td><td><span class="chip-tag">Admin</span></td><td>Sales</td><td><span class="pill pill-success">Aktif</span></td><td class="text-muted-c">15 Apr 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Gilang Nugraha</td><td>gilang@nexora.id</td><td><span class="chip-tag">Editor</span></td><td>Engineering</td><td><span class="pill pill-warning">Pending</span></td><td class="text-muted-c">03 May 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Hana Permata</td><td>hana@nexora.id</td><td><span class="chip-tag">Admin</span></td><td>Finance</td><td><span class="pill pill-success">Aktif</span></td><td class="text-muted-c">19 May 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Indra Lesmana</td><td>indra@nexora.id</td><td><span class="chip-tag">Viewer</span></td><td>Support</td><td><span class="pill pill-neutral">Nonaktif</span></td><td class="text-muted-c">02 Jun 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Joko Santoso</td><td>joko@nexora.id</td><td><span class="chip-tag">Editor</span></td><td>Konten</td><td><span class="pill pill-success">Aktif</span></td><td class="text-muted-c">10 Jun 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
                <tr><td>Karenina Dewi</td><td>karen@nexora.id</td><td><span class="chip-tag">Admin</span></td><td>HR</td><td><span class="pill pill-success">Aktif</span></td><td class="text-muted-c">18 Jun 2026</td><td><button class="btn btn-ghost btn-icon-sq btn-sm"><i class="bi bi-pencil"></i></button></td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/umd/simple-datatables.js"></script>
<script>
  (function () {
    // Initialize DataTable
    var dt = null;
    var tableEl = document.getElementById('usersDataTable');

    function initDataTable() {
      if (typeof simpleDatatables !== 'undefined' && tableEl && !dt) {
        dt = new simpleDatatables.DataTable('#usersDataTable', {
          searchable: true,
          fixedHeight: true,
          perPage: 5,
          perPageSelect: [5, 10, 15, 25],
          labels: {
            placeholder: 'Cari pengguna...',
            perPage: '{select} data per halaman',
            noRows: 'Data tidak ditemukan',
            info: 'Menampilkan {start} - {end} dari {rows} data',
          }
        });
      }
    }

    // Retry in case `simpleDatatables` loads after load event
    initDataTable();
    var retryTimer = setInterval(function () {
      if (typeof simpleDatatables !== 'undefined' && tableEl && !dt) {
        initDataTable();
        clearInterval(retryTimer);
      }
    }, 200);
    setTimeout(function () { clearInterval(retryTimer); }, 5000);

    // External filter: status
    var statusFilter = document.getElementById('dtStatusFilter');
    if (statusFilter) {
      statusFilter.addEventListener('change', function () {
        var val = statusFilter.value.trim().toLowerCase();
        if (dt) {
          dt.search(val || '');
        }
      });
    }
  })();
</script>
@endpush
