@extends('sys_admin.layouts.app')

@section('title', 'Database Backup Management')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-cloud-arrow-down-fill me-2 text-success"></i>Database Backup Management
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Penyimpanan snapshot database SQL multi-client, trigger snapshot on-demand, dan manajemen file cadangan data.
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary-grad rounded-3 px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalTriggerBackup">
        <i class="bi bi-plus-circle me-1.5"></i>Trigger Snapshot Baru
      </button>
    </div>
  </div>

  {{-- 2 Bento Stats Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">TOTAL FILE SNAPSHOT</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <i class="bi bi-file-earmark-code fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0" style="font-size:1.75rem; color:var(--text-primary);">{{ $totalBackups }} File</h3>
        <small class="text-muted-c">Tersimpan di storage lokal terenkripsi</small>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-3 rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.8rem;">TOTAL UKURAN STORAGE</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
            <i class="bi bi-hdd-stack fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-0 text-primary" style="font-size:1.75rem;">{{ $totalSizeMb }} MB</h3>
        <small class="text-muted-c">Penggunaan disk untuk snapshot backup</small>
      </div>
    </div>
  </div>

  {{-- Backups Table Card --}}
  <div class="card rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
    <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex align-items-center justify-content-between">
      <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Daftar Snapshot Backup Database</h6>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" style="font-size:0.85rem;">
          <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
            <tr>
              <th class="ps-4">Client ID</th>
              <th>Nama File Snapshot SQL</th>
              <th>Ukuran File</th>
              <th>Waktu Pembuatan</th>
              <th class="pe-4 text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($backups as $bk)
              <tr style="border-bottom: 1px solid var(--border-subtle);">
                <td class="ps-4">
                  <span class="badge bg-secondary-subtle text-secondary">{{ $bk['client_id'] }}</span>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-1.5">
                    <i class="bi bi-file-earmark-code text-info"></i>
                    <code style="font-size:0.82rem; color:var(--text-primary);">{{ $bk['file_name'] }}</code>
                  </div>
                </td>
                <td>
                  <span class="badge bg-primary-subtle text-primary">{{ $bk['file_size_mb'] }} MB</span>
                </td>
                <td>
                  <span class="text-muted-c">{{ $bk['created_at']->format('d M Y, H:i:s') }}</span>
                  <small class="text-muted-c d-block" style="font-size:0.72rem;">({{ $bk['created_at']->diffForHumans() }})</small>
                </td>
                <td class="pe-4 text-end">
                  <div class="btn-group btn-group-sm">
                    <a href="{{ route('sys_admin.backups.download', [$bk['client_id'], $bk['file_name']]) }}" class="btn btn-outline-primary rounded-2 px-2.5 py-1 me-1" title="Unduh File SQL">
                      <i class="bi bi-download me-1"></i>Unduh
                    </a>
                    <form action="{{ route('sys_admin.backups.destroy', [$bk['client_id'], $bk['file_name']]) }}" method="POST" onsubmit="return confirm('Hapus file backup ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger rounded-2 px-2 py-1" title="Hapus Backup">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted-c">
                  <i class="bi bi-cloud-slash fs-2 d-block mb-2 text-secondary"></i>
                  Belum ada file backup snapshot database yang dibuat.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

{{-- MODAL TRIGGER BACKUP --}}
<div class="modal fade" id="modalTriggerBackup" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; color:var(--text-primary);">
      <div class="modal-header px-4 py-3 border-0" style="border-bottom: 1px solid var(--border-subtle) !important;">
        <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-down text-primary me-2"></i>Trigger Snapshot Backup</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formTriggerBackup" method="POST" action="">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label-modern fw-semibold">Pilih Client Target <span class="text-danger">*</span></label>
            <select name="client_id" id="selectBackupClient" class="form-select form-select-modern" required>
              @foreach($clients as $c)
                <option value="{{ $c->client_id }}">{{ $c->client_name }} ({{ $c->client_id }} &bull; {{ $c->database_name }})</option>
              @endforeach
            </select>
          </div>
          <p class="text-muted-c mb-0" style="font-size:0.82rem;">
            Proses snapshot akan mengekspor skema dan seluruh baris data ke file format <code>.sql</code>.
          </p>
        </div>
        <div class="modal-footer px-4 py-3 border-0" style="border-top: 1px solid var(--border-subtle) !important;">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad px-4 btn-loading" id="btnSubmitBackup">
            <i class="bi bi-camera me-1"></i>Eksekusi Snapshot
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('formTriggerBackup');
  const btn = document.getElementById('btnSubmitBackup');
  const selectClient = document.getElementById('selectBackupClient');

  form.onsubmit = function(e) {
    e.preventDefault();
    const clientId = selectClient.value;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5"></span>Memproses Dump SQL...';

    setTimeout(() => {
      fetch(`/sys_admin/backups/${clientId}/snapshot`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-camera me-1"></i>Eksekusi Snapshot';
        if (data.success) {
          if (typeof NexoraToast === 'function') {
            NexoraToast(data.message, 'success');
          }
          setTimeout(() => location.reload(), 600);
        } else {
          alert(data.message);
        }
      })
      .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-camera me-1"></i>Eksekusi Snapshot';
        alert('Gagal membuat backup.');
      });
    }, 400);
  };
});
</script>
@endpush
