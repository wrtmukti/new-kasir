@forelse($databases as $db)
  <tr id="row-db-{{ $db->client_id }}" style="border-bottom: 1px solid var(--border-subtle);">
    <td class="ps-4">
      <div class="fw-bold text-primary">{{ $db->client?->client_name ?? 'Unknown Client' }}</div>
      <div class="text-muted-c" style="font-size:0.75rem;">
        <span class="badge bg-secondary-subtle text-secondary">{{ $db->client_id }}</span>
        &bull; {{ $db->client?->business_name ?? '-' }}
      </div>
    </td>
    <td>
      <div class="d-flex align-items-center gap-1.5">
        <i class="bi bi-database text-info"></i>
        <code class="fw-semibold" style="font-size:0.82rem; color:var(--text-primary);">{{ $db->database_name }}</code>
      </div>
      <small class="text-muted-c" style="font-size:0.72rem;">{{ $db->server_host }}:{{ $db->server_port }}</small>
    </td>
    <td>
      <span class="badge {{ $db->connection_status === 'connected' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} rounded-pill px-2.5 py-1" id="badge-status-{{ $db->client_id }}">
        <i class="bi bi-circle-fill me-1" style="font-size:0.5rem;"></i>{{ ucfirst($db->connection_status) }}
      </span>
    </td>
    <td>
      <span class="fw-semibold text-success" id="latency-{{ $db->client_id }}">{{ $db->latency_ms }} ms</span>
    </td>
    <td>
      <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-0.5" id="tables-{{ $db->client_id }}">{{ $db->tables_count }} tabel</span>
    </td>
    <td>
      <small class="text-muted-c d-block" style="font-size:0.75rem;" id="health-time-{{ $db->client_id }}">
        {{ $db->last_health_check_at ? $db->last_health_check_at->diffForHumans() : 'Belum dicek' }}
      </small>
    </td>
    <td class="pe-4 text-end">
      <div class="btn-group btn-group-sm">
        <button type="button" class="btn btn-outline-primary rounded-2 px-2.5 py-1 me-1 btn-test-conn" data-client-id="{{ $db->client_id }}" title="Test Ping Koneksi">
          <i class="bi bi-broadcast"></i> Test Ping
        </button>
        <button type="button" class="btn btn-outline-secondary rounded-2 px-2 py-1 btn-run-migrate" data-client-id="{{ $db->client_id }}" title="Jalankan Migrasi Skema">
          <i class="bi bi-arrow-repeat"></i>
        </button>
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="7" class="text-center py-5 text-muted-c">
      <i class="bi bi-database-slash fs-2 d-block mb-2 text-secondary"></i>
      Belum ada koneksi database client terdaftar.
    </td>
  </tr>
@endforelse
