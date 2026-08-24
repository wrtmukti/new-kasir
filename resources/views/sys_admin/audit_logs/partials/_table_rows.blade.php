@forelse($auditLogs as $log)
  <tr style="border-bottom: 1px solid var(--border-subtle);">
    <td class="ps-4">
      <span class="text-muted-c" style="font-size:0.8rem;">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
      <small class="text-muted-c d-block" style="font-size:0.72rem;">({{ $log->created_at->diffForHumans() }})</small>
    </td>
    <td>
      <div class="fw-semibold text-primary" style="font-size:0.85rem;">{{ $log->actor_name ?? 'System' }}</div>
      <span class="badge bg-secondary-subtle text-secondary" style="font-size:0.7rem;">{{ strtoupper($log->actor_role ?? 'SYSTEM') }}</span>
    </td>
    <td>
      <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1" style="font-family: monospace;">
        {{ $log->action }}
      </span>
    </td>
    <td>
      @if($log->client_id)
        <div class="fw-semibold" style="font-size:0.82rem; color:var(--text-primary);">{{ $log->client?->client_name ?? $log->client_id }}</div>
        <small class="text-muted-c">{{ $log->client_id }}</small>
      @else
        <span class="text-muted-c" style="font-size:0.8rem;">Platform Scope</span>
      @endif
    </td>
    <td>
      <div style="font-size:0.82rem;">
        <span class="text-muted-c">{{ $log->target_type }}:</span> <code>{{ $log->target_id ?? '-' }}</code>
      </div>
      @if($log->metadata && count($log->metadata) > 0)
        <small class="text-muted-c text-truncate d-block" style="max-width: 250px; font-size:0.72rem;">
          {{ json_encode($log->metadata) }}
        </small>
      @endif
    </td>
    <td>
      <code style="font-size:0.78rem;">{{ $log->ip_address ?? '127.0.0.1' }}</code>
    </td>
    <td class="pe-4 text-end">
      @if($log->result === 'success')
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5">
          <i class="bi bi-check-circle-fill me-1" style="font-size:0.45rem;"></i>Success
        </span>
      @else
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-0.5">
          <i class="bi bi-x-circle-fill me-1" style="font-size:0.45rem;"></i>Failed
        </span>
      @endif
    </td>
  </tr>
@empty
  <tr>
    <td colspan="7" class="text-center py-5 text-muted-c">
      <i class="bi bi-shield-slash fs-2 d-block mb-2 text-secondary"></i>
      Belum ada riwayat audit log tercatat.
    </td>
  </tr>
@endforelse
