@extends('sys_admin.layouts.app')

@section('title', 'System Health & Infrastructure Monitor')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-heart-pulse-fill me-2 text-danger"></i>System Health & Infrastructure Monitor
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Monitoring ketersediaan server, resource memory/disk, status Central DB, dan latency query isolated client databases.
      </p>
    </div>
    <div class="d-flex align-items-center gap-2">
      <button type="button" class="btn btn-primary-grad rounded-3 px-3 py-2 fw-semibold" id="btnBatchPing">
        <i class="bi bi-broadcast me-1.5"></i>Batch Ping Seluruh Client
      </button>
    </div>
  </div>

  {{-- 4 Environment Status Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">CENTRAL DATABASE</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(16, 185, 129, 0.15); color: #10b981;">
            <i class="bi bi-database-check fs-5"></i>
          </div>
        </div>
        <div class="d-flex align-items-baseline gap-2 mb-1">
          <h3 class="fw-bold mb-0 text-success" style="font-size:1.75rem;">{{ $central['status'] }}</h3>
          <span class="text-muted-c" style="font-size:0.8rem;">({{ $central['latency_ms'] }} ms)</span>
        </div>
        <small class="text-muted-c text-truncate d-block">{{ $central['host'] }} &bull; {{ $central['database'] }}</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">MEMORY USAGE</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">
            <i class="bi bi-cpu fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-1 text-primary" style="font-size:1.75rem;">{{ $server['memory_usage_mb'] }} MB</h3>
        <small class="text-muted-c">Peak: {{ $server['memory_peak_mb'] }} MB &bull; Limit: {{ $server['memory_limit'] }}</small>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">DISK STORAGE</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
            <i class="bi bi-hdd fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-1" style="font-size:1.75rem; color:var(--text-primary);">{{ $disk['used_gb'] }} GB <small class="text-muted-c fs-6">/ {{ $disk['total_gb'] }} GB</small></h3>
        <div class="progress rounded-pill" style="height: 6px; background: var(--bg-elevated-2);">
          <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $disk['used_percent'] }}%"></div>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-xl-3">
      <div class="card p-3 rounded-4 border-0 shadow-sm h-100" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="text-muted-c fw-semibold" style="font-size:0.78rem;">ENVIRONMENT</span>
          <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:36px; height:36px; background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">
            <i class="bi bi-code-square fs-5"></i>
          </div>
        </div>
        <h3 class="fw-bold mb-1 text-primary" style="font-size:1.75rem;">PHP {{ $server['php_version'] }}</h3>
        <small class="text-muted-c">OS: {{ $server['os'] }} ({{ $server['server_software'] }})</small>
      </div>
    </div>
  </div>

  {{-- Client Databases Latency Monitor Table --}}
  <div class="card rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
    <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex align-items-center justify-content-between">
      <div>
        <h6 class="fw-bold mb-0" style="color:var(--text-primary);">
          <i class="bi bi-database me-2 text-info"></i>Multi-Client Database Health & Latency Monitor
        </h6>
        <small class="text-muted-c" style="font-size:0.78rem;">Status koneksi real-time setiap database fisik MySQL terisolasi.</small>
      </div>
      <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5" id="badgeHealthSummary">
        {{ $stats['healthy'] }} / {{ $stats['total'] }} Connected (Rata-rata: {{ $stats['avg_latency'] }} ms)
      </span>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" style="font-size:0.85rem;">
          <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
            <tr>
              <th class="ps-4">Client ID & Nama</th>
              <th>Nama Database Fisik</th>
              <th>Host / Port</th>
              <th>Status Koneksi</th>
              <th>Latency Ping</th>
              <th>Tabel</th>
              <th class="pe-4 text-end">Health Check Terakhir</th>
            </tr>
          </thead>
          <tbody id="healthTableBody">
            @forelse($databases as $db)
              <tr id="row-health-{{ $db->client_id }}" style="border-bottom: 1px solid var(--border-subtle);">
                <td class="ps-4">
                  <div class="fw-bold text-primary">{{ $db->client?->client_name ?? 'Unknown Client' }}</div>
                  <small class="text-muted-c">{{ $db->client_id }}</small>
                </td>
                <td>
                  <code class="fw-semibold" style="color:var(--text-primary);">{{ $db->database_name }}</code>
                </td>
                <td>{{ $db->server_host }}:{{ $db->server_port }}</td>
                <td>
                  <span class="badge {{ $db->connection_status === 'connected' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2.5 py-1" id="health-status-{{ $db->client_id }}">
                    {{ ucfirst($db->connection_status) }}
                  </span>
                </td>
                <td>
                  <span class="fw-semibold text-success" id="health-latency-{{ $db->client_id }}">{{ $db->latency_ms }} ms</span>
                </td>
                <td>{{ $db->tables_count }}</td>
                <td class="pe-4 text-end text-muted-c" id="health-check-{{ $db->client_id }}">
                  {{ $db->last_health_check_at ? $db->last_health_check_at->diffForHumans() : 'Belum dicek' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted-c">Belum ada database client terdaftar.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const btnBatchPing = document.getElementById('btnBatchPing');

  btnBatchPing.onclick = function() {
    const origHtml = btnBatchPing.innerHTML;
    btnBatchPing.disabled = true;
    btnBatchPing.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memeriksa Semua Database...';

    setTimeout(() => {
      fetch("{{ route('sys_admin.health.ping-all') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        btnBatchPing.disabled = false;
        btnBatchPing.innerHTML = origHtml;

        if (data.success) {
          data.results.forEach(res => {
            const statusEl = document.getElementById(`health-status-${res.client_id}`);
            const latencyEl = document.getElementById(`health-latency-${res.client_id}`);
            const checkEl = document.getElementById(`health-check-${res.client_id}`);

            if (statusEl) {
              statusEl.className = res.status === 'connected' ? 'badge bg-success-subtle text-success rounded-pill px-2.5 py-1' : 'badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1';
              statusEl.textContent = res.status.charAt(0).toUpperCase() + res.status.slice(1);
            }
            if (latencyEl) latencyEl.textContent = `${res.latency_ms} ms`;
            if (checkEl) checkEl.textContent = 'Baru saja';
          });

          if (typeof NexoraToast === 'function') {
            NexoraToast(data.message, 'success');
          } else {
            alert(data.message);
          }
        }
      })
      .catch(err => {
        btnBatchPing.disabled = false;
        btnBatchPing.innerHTML = origHtml;
        alert('Gagal menjalankan batch ping.');
      });
    }, 400);
  };
});
</script>
@endpush
