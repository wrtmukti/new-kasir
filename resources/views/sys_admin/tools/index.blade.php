@extends('sys_admin.layouts.app')

@section('title', 'System Tools & Maintenance')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-tools me-2 text-warning"></i>System Tools & Platform Maintenance
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Utilitas pemeliharaan performa, pembersihan cache framework, optimasi konfigurasi, dan manajemen worker antrian.
      </p>
    </div>
  </div>

  {{-- 6 Maintenance Bento Cards --}}
  <div class="row g-4 mb-4">
    {{-- Tool 1: Clear Application Cache --}}
    <div class="col-md-6 col-xl-4">
      <div class="card p-4 rounded-4 border-0 shadow-sm h-100 d-flex flex-column" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
            <i class="bi bi-lightning-charge fs-4"></i>
          </div>
          <span class="badge bg-secondary-subtle text-secondary">Cache</span>
        </div>
        <h5 class="fw-bold mb-1" style="color:var(--text-primary);">Clear App Cache</h5>
        <p class="text-muted-c mb-4" style="font-size:0.82rem; flex-grow:1;">
          Membersihkan seluruh item cache aplikasi yang tersimpan di driver Redis / Database / File.
        </p>
        <button type="button" class="btn btn-outline-primary rounded-3 w-100 fw-semibold btn-run-tool" data-tool="clear_cache">
          <i class="bi bi-trash3 me-1.5"></i>Flush Application Cache
        </button>
      </div>
    </div>

    {{-- Tool 2: Clear Config Cache --}}
    <div class="col-md-6 col-xl-4">
      <div class="card p-4 rounded-4 border-0 shadow-sm h-100 d-flex flex-column" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <i class="bi bi-sliders fs-4"></i>
          </div>
          <span class="badge bg-secondary-subtle text-secondary">Config</span>
        </div>
        <h5 class="fw-bold mb-1" style="color:var(--text-primary);">Clear Config Cache</h5>
        <p class="text-muted-c mb-4" style="font-size:0.82rem; flex-grow:1;">
          Menghapus cache konfigurasi <code>.env</code> dan memuat ulang file setting terbaru.
        </p>
        <button type="button" class="btn btn-outline-success rounded-3 w-100 fw-semibold btn-run-tool" data-tool="clear_config">
          <i class="bi bi-arrow-clockwise me-1.5"></i>Reload Configurations
        </button>
      </div>
    </div>

    {{-- Tool 3: Clear Route Cache --}}
    <div class="col-md-6 col-xl-4">
      <div class="card p-4 rounded-4 border-0 shadow-sm h-100 d-flex flex-column" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px; background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
            <i class="bi bi-signpost-split fs-4"></i>
          </div>
          <span class="badge bg-secondary-subtle text-secondary">Routes</span>
        </div>
        <h5 class="fw-bold mb-1" style="color:var(--text-primary);">Clear Route Cache</h5>
        <p class="text-muted-c mb-4" style="font-size:0.82rem; flex-grow:1;">
          Merefresh seluruh route URL platform dan mendaftarkan ulang route baru.
        </p>
        <button type="button" class="btn btn-outline-primary rounded-3 w-100 fw-semibold btn-run-tool" data-tool="clear_route" style="color:#8b5cf6; border-color:#8b5cf6;">
          <i class="bi bi-signpost me-1.5"></i>Flush Route Cache
        </button>
      </div>
    </div>

    {{-- Tool 4: Clear View Cache --}}
    <div class="col-md-6 col-xl-4">
      <div class="card p-4 rounded-4 border-0 shadow-sm h-100 d-flex flex-column" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px; background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
            <i class="bi bi-file-earmark-richtext fs-4"></i>
          </div>
          <span class="badge bg-secondary-subtle text-secondary">Views</span>
        </div>
        <h5 class="fw-bold mb-1" style="color:var(--text-primary);">Clear Compiled Views</h5>
        <p class="text-muted-c mb-4" style="font-size:0.82rem; flex-grow:1;">
          Menghapus seluruh file kompilasi Blade template untuk memuat tampilan UI termutakhir.
        </p>
        <button type="button" class="btn btn-outline-warning rounded-3 w-100 fw-semibold btn-run-tool" data-tool="clear_view">
          <i class="bi bi-brush me-1.5"></i>Purge Blade Views
        </button>
      </div>
    </div>

    {{-- Tool 5: Optimize Clear --}}
    <div class="col-md-6 col-xl-4">
      <div class="card p-4 rounded-4 border-0 shadow-sm h-100 d-flex flex-column" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px; background: rgba(239, 68, 68, 0.15); color: #ef4444;">
            <i class="bi bi-stars fs-4"></i>
          </div>
          <span class="badge bg-danger-subtle text-danger">Full Reset</span>
        </div>
        <h5 class="fw-bold mb-1" style="color:var(--text-primary);">Optimize Clear All</h5>
        <p class="text-muted-c mb-4" style="font-size:0.82rem; flex-grow:1;">
          Menjalankan pembersihan menyeluruh untuk Config, Route, Cache, dan Blade views sekaligus.
        </p>
        <button type="button" class="btn btn-danger-grad rounded-3 w-100 fw-semibold btn-run-tool" data-tool="optimize">
          <i class="bi bi-stars me-1.5"></i>Full Optimization Clear
        </button>
      </div>
    </div>

    {{-- Tool 6: Restart Queue Worker --}}
    <div class="col-md-6 col-xl-4">
      <div class="card p-4 rounded-4 border-0 shadow-sm h-100 d-flex flex-column" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:42px; height:42px; background: rgba(14, 165, 233, 0.15); color: #0ea5e9;">
            <i class="bi bi-arrow-repeat fs-4"></i>
          </div>
          <span class="badge bg-secondary-subtle text-secondary">Queue</span>
        </div>
        <h5 class="fw-bold mb-1" style="color:var(--text-primary);">Restart Queue Workers</h5>
        <p class="text-muted-c mb-4" style="font-size:0.82rem; flex-grow:1;">
          Mengirim sinyal graceful restart ke worker antrian latar belakang untuk memuat kode terbaru.
        </p>
        <button type="button" class="btn btn-outline-info rounded-3 w-100 fw-semibold btn-run-tool" data-tool="queue_restart">
          <i class="bi bi-arrow-clockwise me-1.5"></i>Restart Queue Workers
        </button>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-run-tool').forEach(btn => {
    btn.onclick = function() {
      const tool = this.getAttribute('data-tool');
      const origHtml = this.innerHTML;
      this.disabled = true;
      this.innerHTML = '<span class="spinner-border spinner-border-sm me-1.5"></span>Memproses...';

      setTimeout(() => {
        fetch("{{ route('sys_admin.tools.run') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ tool: tool })
        })
        .then(res => res.json())
        .then(data => {
          this.disabled = false;
          this.innerHTML = origHtml;

          if (data.success) {
            if (typeof NexoraToast === 'function') {
              NexoraToast(data.message, 'success');
            } else {
              alert(data.message);
            }
          } else {
            alert(data.message);
          }
        })
        .catch(err => {
          this.disabled = false;
          this.innerHTML = origHtml;
          alert('Koneksi terputus.');
        });
      }, 400);
    };
  });
});
</script>
@endpush
