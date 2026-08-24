@extends('sys_admin.layouts.app')

@section('title', 'Subscriptions Management')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-credit-card-2-front-fill me-2 text-primary"></i>Subscriptions Management
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Monitoring masa aktif langganan client, status trial/expired, dan perpanjangan paket SaaS.
      </p>
    </div>
  </div>

  {{-- Subscriptions Table Card --}}
  <div class="card rounded-4 border-0 shadow-sm" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important;">
    <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex align-items-center justify-content-between">
      <h6 class="fw-bold mb-0" style="color:var(--text-primary);">Daftar Langganan SaaS Seluruh Client</h6>
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" style="font-size:0.85rem;">
          <thead style="background: var(--bg-elevated-2); color: var(--text-secondary);">
            <tr>
              <th class="ps-4">Klien / Client</th>
              <th>Paket Langganan</th>
              <th>Periode Mulai</th>
              <th>Tanggal Kadaluarsa</th>
              <th>Status</th>
              <th>Ref Billing</th>
              <th class="pe-4 text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($subscriptions as $sub)
              <tr style="border-bottom: 1px solid var(--border-subtle);">
                <td class="ps-4">
                  <div class="fw-bold text-primary">{{ $sub->client?->client_name ?? 'Unknown' }}</div>
                  <small class="text-muted-c">{{ $sub->client_id }}</small>
                </td>
                <td>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">
                    {{ $sub->plan?->plan_name }}
                  </span>
                </td>
                <td>{{ $sub->start_date ? $sub->start_date->format('d M Y') : '-' }}</td>
                <td>
                  <span class="fw-semibold {{ $sub->isExpired() ? 'text-danger' : 'text-success' }}">
                    {{ $sub->expired_date ? $sub->expired_date->format('d M Y') : '-' }}
                  </span>
                  <small class="text-muted-c d-block" style="font-size:0.72rem;">({{ $sub->daysRemaining() }} hari lagi)</small>
                </td>
                <td>
                  @if($sub->status === 'active')
                    <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Active</span>
                  @elseif($sub->status === 'trial')
                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2.5 py-1">Trial</span>
                  @elseif($sub->status === 'expired')
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">Expired</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">{{ ucfirst($sub->status) }}</span>
                  @endif
                </td>
                <td><span class="badge bg-secondary-subtle text-secondary">{{ $sub->billing_reference ?? '-' }}</span></td>
                <td class="pe-4 text-end">
                  <button type="button" class="btn btn-sm btn-outline-primary rounded-2 px-2.5 py-1 btn-extend" data-id="{{ $sub->id }}" data-client="{{ $sub->client_id }}" data-expired="{{ $sub->expired_date ? $sub->expired_date->format('Y-m-d') : '' }}" data-bs-toggle="modal" data-bs-target="#modalExtendSub">
                    <i class="bi bi-calendar-plus me-1"></i>Perpanjang
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted-c">
                  <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                  Belum ada subscription terdaftar.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card-footer bg-transparent border-0 px-4 py-3 d-flex align-items-center justify-content-between" style="border-top: 1px solid var(--border-subtle) !important;">
      <div class="text-muted-c" style="font-size:0.82rem;">Total {{ $subscriptions->total() }} langganan</div>
      <div>{{ $subscriptions->links('vendor.pagination.modern') }}</div>
    </div>
  </div>

</div>

{{-- MODAL EXTEND SUBSCRIPTION --}}
<div class="modal fade" id="modalExtendSub" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0" style="background: var(--bg-elevated); border: 1px solid var(--border-subtle) !important; color:var(--text-primary);">
      <div class="modal-header px-4 py-3 border-0" style="border-bottom: 1px solid var(--border-subtle) !important;">
        <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus text-primary me-2"></i>Perpanjang Langganan Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formExtendSub" method="POST" action="">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label-modern fw-semibold">Pilih Paket SaaS <span class="text-danger">*</span></label>
            <select name="plan_id" class="form-select form-select-modern" required>
              @foreach($plans as $p)
                <option value="{{ $p->id }}">{{ $p->plan_name }} (Rp {{ number_format($p->price_monthly, 0, ',', '.') }}/bln)</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label-modern fw-semibold">Tanggal Kadaluarsa Baru <span class="text-danger">*</span></label>
            <input type="date" name="expired_date" id="inputExtExpired" class="form-control form-control-modern" required>
          </div>
          <div class="mb-3">
            <label class="form-label-modern fw-semibold">Status Langganan <span class="text-danger">*</span></label>
            <select name="status" class="form-select form-select-modern" required>
              <option value="active">Active (Berlangganan Aktif)</option>
              <option value="trial">Trial (Uji Coba)</option>
              <option value="suspended">Suspended</option>
            </select>
          </div>
        </div>
        <div class="modal-footer px-4 py-3 border-0" style="border-top: 1px solid var(--border-subtle) !important;">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary-grad px-4 btn-loading">Simpan Perpanjangan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.btn-extend').forEach(btn => {
    btn.onclick = function() {
      const id = this.getAttribute('data-id');
      const expired = this.getAttribute('data-expired');
      const form = document.getElementById('formExtendSub');
      form.action = `/sys_admin/subscriptions/${id}/extend`;
      document.getElementById('inputExtExpired').value = expired;
    };
  });
});
</script>
@endpush
