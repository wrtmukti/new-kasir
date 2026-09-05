@extends('admin.layouts.app')

@section('title', 'Manajemen Cabang — Portal Owner')

@php $activeMenu = 'owner-branches' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>Manajemen Cabang</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Owner</span>
      <i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Manajemen Cabang</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-primary-grad" data-bs-toggle="modal" data-bs-target="#modalCreateBranch">
      <i class="bi bi-plus-circle me-1"></i> Tambah Cabang
    </button>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: var(--radius-sm);">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(isset($errors) && $errors->any())
  <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="border-radius: var(--radius-sm);">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan validasi:
    <ul class="mb-0 small mt-1 ps-3">
      @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- STAT CARDS RINGKASAN CABANG -->
<div class="row g-3 mb-4">
  <!-- Total Cabang Aktif -->
  <div class="col-md-4 col-sm-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: rgba(99,102,241,0.12); color: var(--accent-1);">
          <i class="bi bi-buildings-fill"></i>
        </div>
        <div class="stat-value text-mono" style="color: var(--text-primary);">
          {{ $outlets->where('outlet_status', 1)->count() }}
          <span class="text-muted-c" style="font-size: 0.95rem; font-weight: normal;">/ {{ $outlets->count() }} Cabang</span>
        </div>
        <div class="stat-label">Cabang Aktif Beroperasi</div>
        <span class="stat-trend up mt-2">
          <i class="bi bi-check2-circle"></i> Terhubung Live
        </span>
      </div>
    </div>
  </div>

  <!-- Total Omzet Konsolidasi Bulan Ini -->
  <div class="col-md-4 col-sm-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: var(--success-bg); color: var(--success);">
          <i class="bi bi-cash-stack"></i>
        </div>
        <div class="stat-value text-mono text-success">
          Rp {{ number_format($totalMonthlyRevenue, 0, ',', '.') }}
        </div>
        <div class="stat-label">Total Omzet Konsolidasi (MTD)</div>
        <span class="stat-trend up mt-2">
          <i class="bi bi-receipt"></i> {{ number_format($totalMonthlyTransactions) }} Pesanan Selesai
        </span>
      </div>
    </div>
  </div>

  <!-- Standardisasi Master Menu & Shift -->
  <div class="col-md-4 col-sm-12">
    <div class="card card-glow h-100">
      <div class="card-inner card-body stat-card">
        <div class="stat-icon" style="background: rgba(99,102,241,0.12); color: var(--accent-1);">
          <i class="bi bi-box-seam-fill"></i>
        </div>
        <div class="stat-value text-mono" style="color: var(--text-primary);">
          {{ number_format($totalMasterProducts) }}
          <span class="text-muted-c" style="font-size: 0.95rem; font-weight: normal;">Menu Aktif</span>
        </div>
        <div class="stat-label">Standarisasi Master Holding</div>
        <span class="stat-trend mt-2" style="background: rgba(99,102,241,0.12); color: var(--accent-1);">
          <i class="bi bi-clock"></i> Cut-Off {{ $commonCutoff }} WIB
        </span>
      </div>
    </div>
  </div>
</div>

<!-- DAFTAR KARTU CABANG -->
<div class="row g-4 mb-4">
  @forelse($outlets as $branch)
    <div class="col-lg-6 col-12">
      <div class="card card-hover-lift h-100">
        <div class="card-body px-4 py-3.5 d-flex flex-column justify-content-between">
          <div>
            <!-- Header Kartu: Identitas Cabang & Status Shift -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
              <div class="d-flex align-items-center gap-2">
                <span class="pill pill-primary fw-bold text-mono" style="font-size: 0.82rem; padding: 3px 10px;">{{ $branch->outlet_code }}</span>
                <span class="pill pill-neutral fw-semibold" style="font-size: 0.8rem; padding: 3px 10px;">
                  <i class="bi bi-geo-alt me-1 text-primary"></i>{{ $branch->outlet_branch ?? 'Cabang' }}
                </span>
              </div>
              @if($branch->is_shift_open)
                <span class="pill pill-success text-mono fw-medium" style="font-size: 0.8rem; padding: 3px 10px;">
                  <span class="status-dot live me-1.5"></span>Shift Buka
                </span>
              @elseif($branch->outlet_status == 1)
                <span class="pill pill-neutral text-mono" style="font-size: 0.8rem; padding: 3px 10px;">
                  <span class="status-dot me-1.5"></span>Tutup Shift
                </span>
              @else
                <span class="pill pill-danger text-mono" style="font-size: 0.8rem; padding: 3px 10px;">Non-Aktif</span>
              @endif
            </div>

            <!-- Nama & Alamat Lengkap Cabang -->
            <div class="mb-3">
              <h4 class="fw-bold mb-1" style="color: var(--text-primary); font-size: 1.22rem; letter-spacing: -0.2px;">
                {{ $branch->outlet_name }}
              </h4>
              <div class="text-muted-c d-flex align-items-start gap-2 mb-1" style="font-size: 0.86rem; line-height: 1.45;">
                <i class="bi bi-geo-alt-fill text-primary mt-0.5 flex-shrink-0"></i>
                <span>{{ $branch->outlet_address ?? 'Alamat operasional belum diatur' }}</span>
              </div>
              @if($branch->outlet_phone)
                <div class="text-muted-c small d-flex align-items-center gap-2">
                  <i class="bi bi-telephone text-muted-c flex-shrink-0" style="font-size: 0.8rem;"></i>
                  <span class="text-mono">{{ $branch->outlet_phone }}</span>
                </div>
              @endif
            </div>

            <!-- MODUL 1: Performa Finansial & Penjualan Aktual (Full-Width) -->
            <div class="rounded-3 mb-3" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); padding: 0.95rem 1.25rem;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                  <div class="stat-icon" style="width: 28px; height: 28px; border-radius: var(--radius-sm); background: var(--success-bg); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 0.88rem;">
                    <i class="bi bi-cash-stack"></i>
                  </div>
                  <span class="text-uppercase fw-bold text-muted-c" style="font-size: 0.72rem; letter-spacing: 0.5px;">Performa Penjualan (MTD)</span>
                </div>
                <span class="pill pill-primary text-mono" style="font-size: 0.68rem; padding: 2px 8px;">Bulan Ini</span>
              </div>

              <!-- Nilai Omzet Utama -->
              <div class="mb-2">
                <div class="text-muted-c small mb-0.5" style="font-size: 0.76rem;">Total Omzet Bulan Berjalan</div>
                <div class="stat-value text-mono fw-bold text-success" style="font-size: 1.45rem; line-height: 1.2;">
                  Rp {{ number_format($branch->monthly_revenue, 0, ',', '.') }}
                </div>
              </div>

              <!-- Sub-Metrik: Hari Ini & Total Pesanan (Tanpa Garis Horizontal yang Tabrakan) -->
              <div class="row g-2 pt-1">
                <div class="col-6">
                  <span class="text-muted-c d-block" style="font-size: 0.74rem;">Penjualan Hari Ini:</span>
                  <strong class="text-mono text-primary" style="font-size: 0.95rem;">
                    Rp {{ number_format($branch->today_revenue, 0, ',', '.') }}
                  </strong>
                </div>
                <div class="col-6 text-end">
                  <span class="text-muted-c d-block" style="font-size: 0.74rem;">Total Pesanan Selesai:</span>
                  <strong class="text-mono" style="color: var(--text-primary); font-size: 0.95rem;">
                    {{ number_format($branch->monthly_transactions_count) }} <span class="fw-normal text-muted-c" style="font-size: 0.75rem;">pesanan</span>
                  </strong>
                </div>
              </div>
            </div>

            <!-- MODUL 2: Status Operasional & Kasir (Full-Width) -->
            <div class="rounded-3 mb-3" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); padding: 0.95rem 1.25rem;">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="d-flex align-items-center gap-2">
                  <div class="stat-icon" style="width: 28px; height: 28px; border-radius: var(--radius-sm); background: rgba(99,102,241,0.12); color: var(--accent-1); display: flex; align-items: center; justify-content: center; font-size: 0.88rem;">
                    <i class="bi bi-clock-history"></i>
                  </div>
                  <span class="text-uppercase fw-bold text-muted-c" style="font-size: 0.72rem; letter-spacing: 0.5px;">Operasional & Kasir</span>
                </div>
                <span class="pill pill-neutral text-mono" style="font-size: 0.68rem; padding: 2px 8px;">
                  Cut-Off {{ $branch->shiftSetting->daily_cutoff_time ?? '02:00:00' }} WIB
                </span>
              </div>

              <div class="d-flex flex-column" style="font-size: 0.84rem;">
                <!-- Kasir Aktif Bertugas -->
                <div class="d-flex align-items-center justify-content-between py-1.5">
                  <div class="d-flex align-items-center gap-2 text-muted-c">
                    <i class="bi bi-person-badge text-primary" style="font-size: 1rem;"></i>
                    <span>Kasir Bertugas:</span>
                  </div>
                  <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                    <strong style="color: var(--text-primary);">{{ $branch->cashier_on_duty }}</strong>
                    <span class="pill pill-primary text-mono" style="font-size: 0.68rem; padding: 1px 7px;">{{ $branch->shift_name_on_duty }}</span>
                  </div>
                </div>

                <!-- Katalog Menu POS -->
                <div class="d-flex align-items-center justify-content-between py-1.5 border-top" style="border-color: var(--border-subtle) !important;">
                  <div class="d-flex align-items-center gap-2 text-muted-c">
                    <i class="bi bi-box-seam text-info" style="font-size: 1rem;"></i>
                    <span>Katalog Menu POS:</span>
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <strong class="text-mono" style="color: var(--text-primary);">{{ $branch->active_menu_count }} Produk Aktif</strong>
                    <span class="pill pill-neutral" style="font-size: 0.68rem; padding: 1px 7px;">Siap Jual</span>
                  </div>
                </div>

                <!-- Jadwal Cut-Off Harian -->
                <div class="d-flex align-items-center justify-content-between py-1.5 border-top" style="border-color: var(--border-subtle) !important;">
                  <div class="d-flex align-items-center gap-2 text-muted-c">
                    <i class="bi bi-calendar2-check text-warning" style="font-size: 1rem;"></i>
                    <span>Siklus Cut-Off Toko:</span>
                  </div>
                  <div>
                    <span class="text-mono fw-semibold" style="color: var(--text-secondary);">Setiap {{ $branch->shiftSetting->daily_cutoff_time ?? '02:00:00' }} WIB</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TOMBOL AKSI LENGKAP -->
          <div class="mt-2.5 pt-3 border-top" style="border-color: var(--border-subtle) !important;">
            <a href="{{ route('admin.switch-outlet', ['outlet_id' => $branch->outlet_id, 'redirect_to' => 'store']) }}" class="btn btn-primary-grad w-100 py-2 mb-2 fw-semibold d-flex align-items-center justify-content-center gap-2" style="font-size: 0.9rem; border-radius: var(--radius-sm); box-shadow: 0 2px 8px rgba(99,102,241,0.25);">
              <i class="bi bi-shop-window"></i> Masuk Toko Cabang
            </a>
            <div class="d-flex gap-2.5">
              <button type="button" class="btn btn-outline-soft flex-fill py-2 d-flex align-items-center justify-content-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalEditBranch-{{ $branch->outlet_id }}" title="Edit Informasi Cabang" style="font-size: 0.84rem;">
                <i class="bi bi-pencil-square"></i> Edit Info Cabang
              </button>
              <form method="POST" action="{{ route('owner.branches.destroy', $branch->outlet_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan cabang ini?');" class="m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-soft text-danger px-3 py-2 d-flex align-items-center justify-content-center gap-1.5" title="Nonaktifkan Cabang" style="font-size: 0.84rem;">
                  <i class="bi bi-trash3"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT CABANG -->
    <div class="modal fade" id="modalEditBranch-{{ $branch->outlet_id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md);">
          <div class="modal-header" style="border-bottom: 1px solid var(--border-subtle);">
            <h6 class="modal-title fw-bold" style="color: var(--text-primary); font-size: 1rem;">
              <i class="bi bi-pencil-square text-primary me-1.5"></i>Edit Cabang: {{ $branch->outlet_name }}
            </h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" action="{{ route('owner.branches.update', $branch->outlet_id) }}">
            @csrf
            <div class="modal-body p-3">
              <div class="mb-3">
                <label class="form-label-modern mb-1.5">Nama Cabang</label>
                <input type="text" name="outlet_name" value="{{ old('outlet_name', $branch->outlet_name) }}" class="form-control-modern" required>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label class="form-label-modern mb-1.5">Kode Cabang</label>
                  <input type="text" name="outlet_code" value="{{ old('outlet_code', $branch->outlet_code) }}" class="form-control-modern text-mono" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label-modern mb-1.5">Wilayah / Kota</label>
                  <input type="text" name="outlet_branch" value="{{ old('outlet_branch', $branch->outlet_branch) }}" class="form-control-modern">
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label class="form-label-modern mb-1.5">No. Telepon</label>
                  <input type="text" name="outlet_phone" value="{{ old('outlet_phone', $branch->outlet_phone) }}" class="form-control-modern">
                </div>
                <div class="col-md-6">
                  <label class="form-label-modern mb-1.5">Jam Cut-Off Shift</label>
                  <input type="time" name="cutoff_time" value="{{ old('cutoff_time', $branch->shiftSetting->daily_cutoff_time ?? '03:00') }}" class="form-control-modern text-mono">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label-modern mb-1.5">Alamat Lengkap</label>
                <textarea name="outlet_address" class="form-control-modern" rows="2">{{ old('outlet_address', $branch->outlet_address) }}</textarea>
              </div>

              <div class="mb-2">
                <label class="form-label-modern mb-1.5">Status Operasional</label>
                <select name="outlet_status" class="form-select-modern">
                  <option value="1" {{ $branch->outlet_status == 1 ? 'selected' : '' }}>Aktif Beroperasi</option>
                  <option value="0" {{ $branch->outlet_status == 0 ? 'selected' : '' }}>Non-Aktif / Tutup Sementara</option>
                </select>
              </div>
            </div>
            <div class="modal-footer p-2.5" style="border-top: 1px solid var(--border-subtle);">
              <button type="button" class="btn btn-outline-soft btn-sm" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary-grad btn-sm">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="card text-center py-5">
        <div class="card-body">
          <i class="bi bi-buildings fs-1 mb-2 text-muted-c"></i>
          <h5 class="fw-bold" style="color: var(--text-primary);">Belum Ada Cabang Outlet</h5>
          <p class="text-muted-c small mb-3">Klik tombol Tambah Cabang di atas untuk mendaftarkan cabang pertama Anda.</p>
        </div>
      </div>
    </div>
  @endforelse
</div>

<!-- MODAL TAMBAH CABANG BARU -->
<div class="modal fade" id="modalCreateBranch" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md);">
      <div class="modal-header" style="border-bottom: 1px solid var(--border-subtle);">
        <h6 class="modal-title fw-bold" style="color: var(--text-primary); font-size: 1rem;">
          <i class="bi bi-plus-circle text-primary me-1.5"></i>Tambah Cabang Baru
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('owner.branches.store') }}">
        @csrf
        <div class="modal-body p-3">
          <div class="mb-3">
            <label class="form-label-modern mb-1.5">Nama Cabang Restoran</label>
            <input type="text" name="outlet_name" placeholder="Contoh: Kopi Senja Cabang Bandung" class="form-control-modern" required>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label-modern mb-1.5">Kode Cabang</label>
              <input type="text" name="outlet_code" placeholder="Contoh: KS-BDG" class="form-control-modern text-mono" required>
            </div>
            <div class="col-md-6">
              <label class="form-label-modern mb-1.5">Wilayah / Kota</label>
              <input type="text" name="outlet_branch" placeholder="Contoh: Bandung" class="form-control-modern">
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label-modern mb-1.5">No. Telepon</label>
              <input type="text" name="outlet_phone" placeholder="0812xxxx" class="form-control-modern">
            </div>
            <div class="col-md-6">
              <label class="form-label-modern mb-1.5">Jam Cut-Off Shift</label>
              <input type="time" name="cutoff_time" value="03:00" class="form-control-modern text-mono">
            </div>
          </div>

          <div class="mb-2">
            <label class="form-label-modern mb-1.5">Alamat Lengkap</label>
            <textarea name="outlet_address" placeholder="Jl. Ir. H. Juanda No. 102, Bandung" class="form-control-modern" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer p-2.5" style="border-top: 1px solid var(--border-subtle);">
          <button type="button" class="btn btn-outline-soft btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad btn-sm">
            <i class="bi bi-save me-1"></i> Simpan Cabang
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
