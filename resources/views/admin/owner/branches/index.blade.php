@extends('admin.layouts.app')

@section('title', 'Manajemen Cabang — Portal Owner')

@php $activeMenu = 'owner-branches' @endphp

@section('content')
<!-- PAGE HEADER -->
<div class="page-header">
  <div>
    <h1>🏢 Manajemen Jaringan Cabang &amp; Outlet</h1>
    <div class="breadcrumb-trail">
      <a href="{{ route('admin.dashboard') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Portal Owner</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Manajemen Cabang</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateBranch">
      <i class="bi bi-plus-circle me-1"></i> Tambah Cabang Baru
    </button>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(isset($errors) && $errors->any())
  <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px;">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan validasi:
    <ul class="mb-0 small mt-1 ps-3">
      @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- DAFTAR KARTU CABANG -->
<div class="row g-3">
  @forelse($outlets as $branch)
    <div class="col-lg-4 col-md-6">
      <div class="card h-100" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
        <div class="card-body p-3.5 d-flex flex-column justify-content-between">
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="badge badge-soft-primary fw-bold">{{ $branch->outlet_code }}</span>
              @if($branch->outlet_status == 1)
                <span class="badge badge-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
              @else
                <span class="badge badge-secondary">Non-Aktif</span>
              @endif
            </div>

            <h5 class="fw-bold mb-1" style="color: var(--text-primary);">{{ $branch->outlet_name }}</h5>
            <div class="text-muted-c small mb-3">
              <i class="bi bi-geo-alt me-1"></i>{{ $branch->outlet_branch ?? 'Pusat' }} &bull; {{ $branch->outlet_address ?? 'Alamat belum diatur' }}
            </div>

            <div class="p-2.5 rounded mb-3" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle); font-size: 0.82rem;">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-secondary-c">Jam Cut-Off Shift:</span>
                <strong style="color: var(--text-primary);">{{ $branch->shiftSetting->daily_cutoff_time ?? '03:00' }} WIB</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-secondary-c">Telepon Outlet:</span>
                <strong style="color: var(--text-primary);">{{ $branch->outlet_phone ?? '-' }}</strong>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 pt-2 border-top" style="border-color: var(--border-subtle) !important;">
            <button type="button" class="btn btn-outline-soft btn-sm flex-fill" data-bs-toggle="modal" data-bs-target="#modalEditBranch-{{ $branch->outlet_id }}">
              <i class="bi bi-pencil me-1"></i> Edit Cabang
            </button>
            <form method="POST" action="{{ route('admin.owner.branches.destroy', $branch->outlet_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan cabang ini?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL EDIT CABANG -->
    <div class="modal fade" id="modalEditBranch-{{ $branch->outlet_id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
          <div class="modal-header" style="border-bottom: 1px solid var(--border-subtle);">
            <h5 class="modal-title fw-bold" style="color: var(--text-primary);">Edit Cabang: {{ $branch->outlet_name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" action="{{ route('admin.owner.branches.update', $branch->outlet_id) }}">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label small fw-bold text-secondary-c">Nama Cabang:</label>
                <input type="text" name="outlet_name" value="{{ old('outlet_name', $branch->outlet_name) }}" class="form-control" required style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
              </div>

              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label class="form-label small fw-bold text-secondary-c">Kode Cabang (Singkatan):</label>
                  <input type="text" name="outlet_code" value="{{ old('outlet_code', $branch->outlet_code) }}" class="form-control" required style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold text-secondary-c">Wilayah / Kota:</label>
                  <input type="text" name="outlet_branch" value="{{ old('outlet_branch', $branch->outlet_branch) }}" class="form-control" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
                </div>
              </div>

              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label class="form-label small fw-bold text-secondary-c">No. Telepon:</label>
                  <input type="text" name="outlet_phone" value="{{ old('outlet_phone', $branch->outlet_phone) }}" class="form-control" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
                </div>
                <div class="col-md-6">
                  <label class="form-label small fw-bold text-secondary-c">Jam Cut-Off Shift:</label>
                  <input type="time" name="cutoff_time" value="{{ old('cutoff_time', $branch->shiftSetting->daily_cutoff_time ?? '03:00') }}" class="form-control" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-secondary-c">Alamat Lengkap:</label>
                <textarea name="outlet_address" class="form-control" rows="2" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">{{ old('outlet_address', $branch->outlet_address) }}</textarea>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-bold text-secondary-c">Status Cabang:</label>
                <select name="outlet_status" class="form-select" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
                  <option value="1" {{ $branch->outlet_status == 1 ? 'selected' : '' }}>Aktif Beroperasi</option>
                  <option value="0" {{ $branch->outlet_status == 0 ? 'selected' : '' }}>Non-Aktif / Tutup Sementara</option>
                </select>
              </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-subtle);">
              <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="col-12">
      <div class="text-center py-5 text-muted-c card" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
        <i class="bi bi-buildings fs-1 mb-2"></i>
        <h5>Belum Ada Cabang Outlet</h5>
        <p class="small mb-3">Klik tombol Tambah Cabang Baru di atas untuk mendaftarkan outlet pertama Anda.</p>
      </div>
    </div>
  @endforelse
</div>

<!-- MODAL TAMBAH CABANG BARU -->
<div class="modal fade" id="modalCreateBranch" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: 16px;">
      <div class="modal-header" style="border-bottom: 1px solid var(--border-subtle);">
        <h5 class="modal-title fw-bold" style="color: var(--text-primary);"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Cabang Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.owner.branches.store') }}">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small fw-bold text-secondary-c">Nama Cabang Restoran:</label>
            <input type="text" name="outlet_name" placeholder="Contoh: Kopi Senja Cabang Bandung" class="form-control" required style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary-c">Kode Cabang (Singkatan):</label>
              <input type="text" name="outlet_code" placeholder="Contoh: BDG-01" class="form-control" required style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary-c">Wilayah / Kota:</label>
              <input type="text" name="outlet_branch" placeholder="Contoh: Bandung" class="form-control" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary-c">No. Telepon Outlet:</label>
              <input type="text" name="outlet_phone" placeholder="0812xxxx" class="form-control" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-secondary-c">Jam Cut-Off Shift:</label>
              <input type="time" name="cutoff_time" value="03:00" class="form-control" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-bold text-secondary-c">Alamat Lengkap:</label>
            <textarea name="outlet_address" placeholder="Jl. Dago No. 123, Bandung" class="form-control" rows="2" style="background: var(--bg-elevated); color: var(--text-primary); border-color: var(--border-subtle);"></textarea>
          </div>
        </div>
        <div class="modal-footer" style="border-top: 1px solid var(--border-subtle);">
          <button type="button" class="btn btn-outline-soft" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Cabang</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
