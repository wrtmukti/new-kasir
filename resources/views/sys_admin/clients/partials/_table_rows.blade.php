@forelse($clients as $c)
  <tr id="row-client-{{ $c->client_id }}" style="border-bottom: 1px solid var(--border-subtle);">
    <td class="ps-4">
      <div class="d-flex align-items-center gap-2.5">
        <div class="rounded-3 d-flex align-items-center justify-content-center fw-bold text-white shadow-sm flex-shrink-0" style="width:36px; height:36px; background: linear-gradient(135deg, #3b82f6, #6366f1); font-size:0.85rem;">
          {{ strtoupper(substr($c->client_name, 0, 2)) }}
        </div>
        <div>
          <a href="{{ route('sys_admin.clients.show', $c->client_id) }}" class="fw-bold text-primary text-decoration-none">
            {{ $c->client_name }}
          </a>
          <div class="text-muted-c" style="font-size:0.75rem;">
            <span class="badge bg-secondary-subtle text-secondary">{{ $c->client_id }}</span>
            @if($c->client_code)
              <span class="badge bg-primary-subtle text-primary">{{ $c->client_code }}</span>
            @endif
            &bull; {{ $c->business_name ?? '-' }}
          </div>
        </div>
      </div>
    </td>
    <td>
      <div class="fw-semibold" style="color:var(--text-primary);">{{ $c->owner_name }}</div>
      <small class="text-muted-c" style="font-size:0.75rem;">{{ $c->owner_email }} &bull; {{ $c->owner_phone ?? '-' }}</small>
    </td>
    <td>
      <div class="d-flex align-items-center gap-1.5">
        <i class="bi bi-database text-info" style="font-size:0.85rem;"></i>
        <code class="fw-semibold" style="font-size:0.8rem; color:var(--text-primary);">{{ $c->database_name }}</code>
      </div>
      <small class="text-muted-c" style="font-size:0.72rem;">{{ $c->databaseConnection?->tables_count ?? 0 }} tabel &bull; {{ $c->databaseConnection?->latency_ms ?? 0 }} ms</small>
    </td>
    <td>
      @php
        $activeSub = $c->activeSubscription;
        $plan = $activeSub?->plan;
      @endphp
      @if($plan)
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
          {{ $plan->plan_name }}
        </span>
        <small class="text-muted-c d-block mt-0.5" style="font-size:0.72rem;">
          @if($activeSub->status === 'trial')
            Trial s/d {{ $activeSub->expired_date ? $activeSub->expired_date->format('d M Y') : '-' }}
          @else
            Exp: {{ $activeSub->expired_date ? $activeSub->expired_date->format('d M Y') : '-' }}
          @endif
        </small>
      @else
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5">No Plan</span>
      @endif
    </td>
    <td>
      @if($c->status === 'active')
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">
          <i class="bi bi-circle-fill me-1" style="font-size:0.45rem;"></i>Active
        </span>
      @elseif($c->status === 'provisioning')
        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1">
          <i class="bi bi-arrow-repeat me-1 spin"></i>Provisioning
        </span>
      @elseif($c->status === 'suspended')
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">
          <i class="bi bi-pause-circle-fill me-1"></i>Suspended
        </span>
      @else
        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
          {{ ucfirst($c->status) }}
        </span>
      @endif
    </td>
    <td class="pe-4 text-end">
      <div class="btn-group btn-group-sm">
        <a href="{{ route('sys_admin.clients.show', $c->client_id) }}" class="btn btn-outline-primary rounded-2 px-2.5 py-1" title="Lihat 8 Tab Detail">
          <i class="bi bi-arrow-right-circle me-1"></i>Detail
        </a>
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="6" class="text-center py-5 text-muted-c">
      <i class="bi bi-buildings fs-2 d-block mb-2 text-secondary"></i>
      Belum ada client yang terdaftar.
    </td>
  </tr>
@endforelse
