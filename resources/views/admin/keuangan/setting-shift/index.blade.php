@extends('admin.layouts.app')

@section('title', 'Master Shift & Jam Cut-Off Restoran')

@php $activeMenu = 'setting-shift' @endphp

@push('styles')
<style>
  .mode-box-card {
    background: var(--bg-subtle, rgba(255, 255, 255, 0.03));
    border: 1.5px solid var(--border-subtle, rgba(255, 255, 255, 0.1));
    border-radius: 14px;
    padding: 1.25rem;
    cursor: pointer;
    transition: all 0.25s ease;
    height: 100%;
    position: relative;
  }

  .mode-box-card:hover {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.05);
    transform: translateY(-2px);
  }

  .mode-box-card.active {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.12);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.25);
  }

  .mode-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    margin-bottom: 0.75rem;
  }

  .modal-content-modern {
    background: var(--bg-elevated, #1e293b);
    border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.12));
    border-radius: 16px;
    color: var(--text-primary, #f8fafc);
  }
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1>Master Shift & Cut-Off Restoran</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Keuangan & Setting</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Master Shift & Cut-off</span>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <a href="{{ route('admin.keuangan.shift-operational.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm">
      <i class="bi bi-person-badge me-1"></i> Sesi Clock-In Kasir
    </a>
    <a href="{{ route('admin.reports.shifts') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 btn-sm">
      <i class="bi bi-shield-lock me-1"></i> Audit Shift Closing
    </a>
  </div>
</div>

<!-- Session Flash Notification Listener -->
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); border-radius: 12px;">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<!-- CARD 1: PENGATURAN JAM CUT-OFF OPERASIONAL RESTO -->
<div class="card mb-4">
  <div class="card-header-flex">
    <h6><i class="bi bi-sliders text-primary me-2"></i>Pengaturan Cut-Off Operasional & Mode Shift</h6>
    <span class="chip-tag" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
      <i class="bi bi-info-circle me-1"></i> Mempengaruhi Penentuan Tanggal Bisnis POS
    </span>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.keuangan.setting-shift.update-cutoff') }}" method="POST" id="formCutoff">
      @csrf
      
      <!-- Option Mode Shift Box -->
      <div class="mb-4">
        <label class="form-label-modern mb-2 fw-bold text-uppercase" style="font-size:0.8rem; letter-spacing:0.5px;">1. Pilih Mode Pengoperasian Shift Kasir</label>
        <div class="row g-3">
          
          <!-- Mode 1: Auto Master Shift -->
          <div class="col-md-4">
            <div class="mode-box-card @if($setting->shift_mode === 'auto_master') active @endif" onclick="selectShiftMode('auto_master')">
              <div class="d-flex align-items-center justify-content-between">
                <div class="mode-icon-circle bg-primary bg-opacity-20 text-primary">
                  <i class="bi bi-calendar-range"></i>
                </div>
                <input type="radio" name="shift_mode" value="auto_master" id="mode_auto_master" class="form-check-input" @if($setting->shift_mode === 'auto_master') checked @endif style="cursor: pointer;">
              </div>
              <div class="fw-bold mb-1" style="color: var(--text-primary, #f8fafc);">Terjadwal (Master Shift)</div>
              <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height: 1.4;">Kasir memilih shift resmi dari daftar master. Jam kerja dan modal awal terisi otomatis sesuai template.</p>
            </div>
          </div>

          <!-- Mode 2: Manual Shift -->
          <div class="col-md-4">
            <div class="mode-box-card @if($setting->shift_mode === 'manual') active @endif" onclick="selectShiftMode('manual')">
              <div class="d-flex align-items-center justify-content-between">
                <div class="mode-icon-circle bg-warning bg-opacity-20 text-warning">
                  <i class="bi bi-pencil-square"></i>
                </div>
                <input type="radio" name="shift_mode" value="manual" id="mode_manual" class="form-check-input" @if($setting->shift_mode === 'manual') checked @endif style="cursor: pointer;">
              </div>
              <div class="fw-bold mb-1" style="color: var(--text-primary, #f8fafc);">Manual / Dinamis</div>
              <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height: 1.4;">Kasir bebas menginput nama shift dan modal kas awal secara fleksibel saat pertama kali bertugas.</p>
            </div>
          </div>

          <!-- Mode 3: Single Daily Shift -->
          <div class="col-md-4">
            <div class="mode-box-card @if($setting->shift_mode === 'single_daily') active @endif" onclick="selectShiftMode('single_daily')">
              <div class="d-flex align-items-center justify-content-between">
                <div class="mode-icon-circle bg-success bg-opacity-20 text-success">
                  <i class="bi bi-sun-fill"></i>
                </div>
                <input type="radio" name="shift_mode" value="single_daily" id="mode_single_daily" class="form-check-input" @if($setting->shift_mode === 'single_daily') checked @endif style="cursor: pointer;">
              </div>
              <div class="fw-bold mb-1" style="color: var(--text-primary, #f8fafc);">Single Daily Shift (Full Day)</div>
              <p class="text-muted-c mb-0" style="font-size:0.82rem; line-height: 1.4;">Hanya 1 sesi shift per hari. Kasir buka 1x di pagi hari dan tutup 1x saat toko selesai beroperasi.</p>
            </div>
          </div>

        </div>
      </div>

      <!-- Jam Cut-Off & Auto Lock -->
      <div class="row g-4 align-items-end pt-2">
        <div class="col-md-4">
          <label for="daily_cutoff_time" class="form-label-modern mb-1 fw-semibold">
            Jam Cut-Off Operasional Harian <span class="text-danger">*</span>
          </label>
          <div class="input-group">
            <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-clock"></i></span>
            <input type="time" name="daily_cutoff_time" id="daily_cutoff_time" class="form-control-modern" value="{{ \Carbon\Carbon::parse($setting->daily_cutoff_time)->format('H:i') }}" required>
          </div>
          <div class="text-muted-c mt-1" style="font-size: 0.78rem;">
            Transaksi setelah jam ini dianggap sebagai <strong>Tanggal Bisnis Baru</strong> (Rekomendasi resto: <code>03:00</code> Pagi).
          </div>
        </div>

        <div class="col-md-5">
          <div class="form-check form-switch pt-2">
            <input class="form-check-input" type="checkbox" name="auto_lock_unclosed" id="auto_lock_unclosed" value="1" @if($setting->auto_lock_unclosed) checked @endif style="width: 2.4em; height: 1.2em; cursor: pointer;">
            <label class="form-check-label fw-semibold ms-2" for="auto_lock_unclosed" style="color: var(--text-primary, #f8fafc);">
              Auto-Lock Shift Kemarin (Strict Protection)
            </label>
          </div>
          <div class="text-muted-c mt-1" style="font-size: 0.78rem;">
            Kunci layar POS jika shift hari kemarin belum di-close oleh kasir sebelumnya.
          </div>
        </div>

        <div class="col-md-3 text-end">
          <button type="submit" class="btn btn-primary-grad w-100 btn-loading">
            <i class="bi bi-save me-1"></i> Simpan Pengaturan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- CARD 2: DAFTAR MASTER SHIFT RESTORAN -->
<div class="card">
  <div class="card-header-flex">
    <div>
      <h6><i class="bi bi-list-stars text-primary me-2"></i>Daftar Master Shift Restoran</h6>
      <div class="text-muted-c" style="font-size:0.8rem;">Master template shift yang dipilih oleh kasir saat membuka kasir di POS.</div>
    </div>
    <button type="button" class="btn btn-primary-grad btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddShift">
      <i class="bi bi-plus-lg me-1"></i>Tambah Master Shift
    </button>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table-modern">
        <thead>
          <tr>
            <th style="width: 70px;">No</th>
            <th>Nama Shift</th>
            <th>Jam Kerja Operasional</th>
            <th>Default Modal Awal Kasir</th>
            <th>Status Active</th>
            <th class="text-end" style="width: 140px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($shifts as $index => $shift)
            <tr>
              <td class="fw-bold text-secondary">#{{ $shift->shift_number }}</td>
              <td>
                <span class="fw-bold" style="color: var(--text-primary, #f8fafc);">{{ $shift->shift_name }}</span>
              </td>
              <td>
                <span class="chip-tag" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                  <i class="bi bi-clock me-1"></i>
                  {{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }} WIB
                </span>
              </td>
              <td>
                <span class="fw-bold text-success">
                  Rp {{ number_format($shift->default_starting_cash, 0, ',', '.') }}
                </span>
              </td>
              <td>
                @if($shift->is_active)
                  <span class="chip-tag" style="background: rgba(34, 197, 94, 0.15); color: #4ade80;"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                @else
                  <span class="chip-tag" style="background: rgba(239, 68, 68, 0.15); color: #f87171;"><i class="bi bi-x-circle-fill"></i> Non-Aktif</span>
                @endif
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-action-icon text-warning me-1" title="Edit Shift" onclick="editShift({{ json_encode($shift) }})">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <form action="{{ route('admin.keuangan.setting-shift.destroy-shift', $shift->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus Master Shift ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-action-icon text-danger" title="Hapus Shift">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted-c">
                <i class="bi bi-exclamation-circle d-block fs-3 mb-2 text-secondary"></i>
                Belum ada data Master Shift. Klik tombol <strong>"Tambah Master Shift"</strong> di kanan atas.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>


<!-- MODAL 1: TAMBAH MASTER SHIFT -->
<div class="modal fade" id="modalAddShift" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-modern">
      <div class="modal-header border-secondary border-opacity-25 px-4 py-3">
        <h5 class="modal-title fw-bold" style="color: var(--text-primary, #f8fafc);">
          <i class="bi bi-plus-circle text-primary me-2"></i>Tambah Master Shift Baru
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.keuangan.setting-shift.store-shift') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="shift_name" class="form-label-modern mb-1 fw-semibold">Nama Shift <span class="text-danger">*</span></label>
            <input type="text" name="shift_name" class="form-control-modern" placeholder="Contoh: Shift 1 Pagi" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label for="start_time" class="form-label-modern mb-1 fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
              <input type="time" name="start_time" class="form-control-modern" value="08:00" required>
            </div>
            <div class="col-6">
              <label for="end_time" class="form-label-modern mb-1 fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
              <input type="time" name="end_time" class="form-control-modern" value="16:00" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="default_starting_cash" class="form-label-modern mb-1 fw-semibold">Default Modal Awal Kasir (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="default_starting_cash" class="form-control-modern" value="300000" step="1000" min="0" required>
          </div>
          <div class="form-check form-switch pt-1">
            <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" value="1" checked style="cursor: pointer;">
            <label class="form-check-label fw-semibold" for="add_is_active" style="color: var(--text-primary, #f8fafc);">Aktifkan Shift Ini</label>
          </div>
        </div>
        <div class="modal-footer border-secondary border-opacity-25 px-4 py-3">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad rounded-pill px-4 btn-loading">Simpan Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- MODAL 2: EDIT MASTER SHIFT -->
<div class="modal fade" id="modalEditShift" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-modern">
      <div class="modal-header border-secondary border-opacity-25 px-4 py-3">
        <h5 class="modal-title fw-bold" style="color: var(--text-primary, #f8fafc);">
          <i class="bi bi-pencil-square text-warning me-2"></i>Edit Master Shift
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditShift" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label for="edit_shift_name" class="form-label-modern mb-1 fw-semibold">Nama Shift <span class="text-danger">*</span></label>
            <input type="text" name="shift_name" id="edit_shift_name" class="form-control-modern" required>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-6">
              <label for="edit_start_time" class="form-label-modern mb-1 fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
              <input type="time" name="start_time" id="edit_start_time" class="form-control-modern" required>
            </div>
            <div class="col-6">
              <label for="edit_end_time" class="form-label-modern mb-1 fw-semibold">Jam Selesai <span class="text-danger">*</span></label>
              <input type="time" name="end_time" id="edit_end_time" class="form-control-modern" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="edit_default_starting_cash" class="form-label-modern mb-1 fw-semibold">Default Modal Awal Kasir (Rp) <span class="text-danger">*</span></label>
            <input type="number" name="default_starting_cash" id="edit_default_starting_cash" class="form-control-modern" step="1000" min="0" required>
          </div>
          <div class="form-check form-switch pt-1">
            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1" style="cursor: pointer;">
            <label class="form-check-label fw-semibold" for="edit_is_active" style="color: var(--text-primary, #f8fafc);">Aktifkan Shift Ini</label>
          </div>
        </div>
        <div class="modal-footer border-secondary border-opacity-25 px-4 py-3">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad rounded-pill px-4 btn-loading">Update Shift</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function selectShiftMode(mode) {
    document.querySelectorAll('.mode-box-card').forEach(box => box.classList.remove('active'));
    const radio = document.getElementById('mode_' + mode);
    if (radio) {
      radio.checked = true;
      radio.closest('.mode-box-card').classList.add('active');
    }
  }

  function editShift(shift) {
    const form = document.getElementById('formEditShift');
    form.action = `/admin/keuangan/setting-shift/${shift.id}/update`;

    document.getElementById('edit_shift_name').value = shift.shift_name;
    document.getElementById('edit_start_time').value = shift.start_time.substring(0, 5);
    document.getElementById('edit_end_time').value = shift.end_time.substring(0, 5);
    document.getElementById('edit_default_starting_cash').value = shift.default_starting_cash;
    document.getElementById('edit_is_active').checked = (shift.is_active == 1);

    const editModal = new bootstrap.Modal(document.getElementById('modalEditShift'));
    editModal.show();
  }

  document.querySelectorAll('.btn-loading').forEach(btn => {
    btn.closest('form')?.addEventListener('submit', function() {
      btn.disabled = true;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menyimpan...`;
      setTimeout(() => {}, 400);
    });
  });
</script>
@endpush
