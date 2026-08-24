@extends('sys_admin.layouts.app')

@section('title', 'SaaS Plans & Tiers Management')

@section('content')
<div class="container-fluid p-0">

  {{-- Page Header --}}
  <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
      <h4 class="fw-bold mb-1" style="font-size:1.45rem; color:var(--text-primary);">
        <i class="bi bi-box-seam-fill me-2 text-danger"></i>Master Plans & SaaS Tiers
      </h4>
      <p class="text-muted-c mb-0" style="font-size:0.85rem;">
        Konfigurasi paket berlangganan, kuota cabang outlet, kasir/staff, dan fitur modular SaaS.
      </p>
    </div>
  </div>

  {{-- Plans Bento Grid --}}
  <div class="row g-4 mb-4">
    @foreach($plans as $plan)
      <div class="col-md-6 col-xl-3">
        <div class="card rounded-4 border-0 shadow-sm p-4 h-100 position-relative" style="background: var(--bg-elevated); border: 1.5px solid var(--border-subtle) !important;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fw-bold">{{ $plan->plan_code }}</span>
            <span class="badge bg-secondary-subtle text-secondary">{{ $plan->badge_label }}</span>
          </div>

          <h5 class="fw-bold mb-1" style="color:var(--text-primary);">{{ $plan->plan_name }}</h5>
          <p class="text-muted-c mb-3" style="font-size:0.78rem; min-height:36px;">{{ $plan->description }}</p>

          <div class="mb-4">
            <div class="fw-bold text-success" style="font-size:1.5rem;">
              @if($plan->price_monthly > 0)
                Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}<small class="text-muted-c fw-normal" style="font-size:0.75rem;"> /bln</small>
              @else
                Gratis
              @endif
            </div>
            <small class="text-muted-c">Tahunan: Rp {{ number_format($plan->price_yearly, 0, ',', '.') }}/thn</small>
          </div>

          <ul class="list-unstyled mb-4" style="font-size:0.82rem; line-height:1.8; color:var(--text-secondary);">
            <li><i class="bi bi-check2-circle text-success me-1.5"></i>Max <strong>{{ $plan->max_outlets }}</strong> Outlet Cabang</li>
            <li><i class="bi bi-check2-circle text-success me-1.5"></i>Max <strong>{{ $plan->max_users }}</strong> Akun Kasir</li>
            <li><i class="bi bi-check2-circle text-success me-1.5"></i>Storage <strong>{{ $plan->max_storage_mb }}</strong> MB</li>
            <li><i class="bi bi-check2-circle text-success me-1.5"></i>Trial <strong>{{ $plan->trial_days }}</strong> Hari</li>
          </ul>

          <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--border-subtle) !important;">
            <small class="text-muted-c">{{ $plan->subscriptions_count }} Penyewa</small>
            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1">Aktif</span>
          </div>
        </div>
      </div>
    @endforeach
  </div>

</div>
@endsection
