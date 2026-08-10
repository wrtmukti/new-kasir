@extends('admin.layouts.app')

@section('title', 'Detail & Riwayat Bahan Mentah')

@php $activeMenu = 'cogs-raw-material' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Detail Bahan Mentah: {{ $cogsRawMaterial->name }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.keuangan.cogs-raw-material.index') }}">Bahan Mentah COGS</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Detail</span>
    </div>
  </div>
  <div>
    <a href="{{ route('admin.keuangan.cogs-raw-material.edit', $cogsRawMaterial) }}" class="btn btn-outline-soft me-2"><i class="bi bi-pencil me-1"></i>Edit</a>
    <a href="{{ route('admin.keuangan.cogs-raw-material.index') }}" class="btn btn-outline-soft"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header-flex">
        <h6>Informasi Bahan Mentah</h6>
      </div>
      <div class="card-body p-0">
        <table class="detail-table">
          <tr><td class="detail-label">Kode</td><td class="detail-value fw-bold">{{ $cogsRawMaterial->raw_material_code }}</td></tr>
          <tr><td class="detail-label">Nama</td><td class="detail-value fw-bold">{{ $cogsRawMaterial->name }}</td></tr>
          <tr><td class="detail-label">Satuan</td><td class="detail-value"><span class="chip-tag">{{ $cogsRawMaterial->unit }}</span></td></tr>
          <tr><td class="detail-label">Stok Fisik</td><td class="detail-value fw-bold">{{ number_format($cogsRawMaterial->amount, 2, ',', '.') }} {{ $cogsRawMaterial->unit }}</td></tr>
          <tr><td class="detail-label">Harga Beli</td><td class="detail-value">Rp {{ number_format($cogsRawMaterial->price_per_unit, 0, ',', '.') }}</td></tr>
          <tr><td class="detail-label">Loss (%)</td><td class="detail-value text-danger fw-bold">{{ number_format($cogsRawMaterial->loss_percent, 1) }}%</td></tr>
          <tr><td class="detail-label">Yield (%)</td><td class="detail-value text-success fw-bold">{{ number_format($cogsRawMaterial->yield_percent, 1) }}%</td></tr>
          <tr><td class="detail-label">Harga Efektif</td><td class="detail-value text-success fw-bold fs-5">Rp {{ number_format($cogsRawMaterial->effective_price, 2, ',', '.') }}</td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header-flex">
        <h6>Audit Trail / Riwayat Perubahan & Opname</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern" style="font-size: 0.88rem;">
            <thead>
              <tr>
                <th class="ps-3">Tanggal</th>
                <th>Aksi</th>
                <th class="text-end">Harga Beli</th>
                <th class="text-center">Susut (%)</th>
                <th class="text-end">Harga Efektif</th>
                <th>Oleh</th>
                <th class="pe-3">Catatan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($cogsRawMaterial->histories->sortByDesc('created_at') as $h)
              <tr>
                <td class="ps-3" style="color: var(--text-secondary);">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  @if($h->action_type == 'create')
                    <span class="badge" style="background: rgba(52, 211, 153, 0.15); color: var(--success);">Create</span>
                  @elseif($h->action_type == 'update')
                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: var(--accent-1);">Update</span>
                  @elseif($h->action_type == 'adjustment')
                    <span class="badge" style="background: rgba(251, 191, 36, 0.15); color: var(--warning);">Opname</span>
                  @elseif($h->action_type == 'production')
                    <span class="badge" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">Produksi Stok</span>
                  @elseif($h->action_type == 'waste')
                    <span class="badge" style="background: rgba(248, 113, 113, 0.15); color: var(--danger);">Waste</span>
                  @elseif($h->action_type == 'delete')
                    <span class="badge" style="background: rgba(248, 113, 113, 0.15); color: var(--danger);">Delete</span>
                  @else
                    <span class="badge" style="background: var(--bg-elevated-2); color: var(--text-secondary);">{{ $h->action_type }}</span>
                  @endif
                </td>
                <td class="text-end" style="color: var(--text-secondary);">Rp {{ number_format($h->price_per_unit, 0, ',', '.') }}</td>
                <td class="text-center" style="color: var(--text-secondary);">{{ number_format($h->loss_percent, 1) }}%</td>
                <td class="text-end fw-bold text-success">Rp {{ number_format($h->effective_price, 2, ',', '.') }}</td>
                <td style="color: var(--text-secondary);">{{ $h->changed_by ?? 'Admin' }}</td>
                <td class="pe-3"><small class="text-muted-c">{{ $h->history_remark }}</small></td>
              </tr>
              @empty
              <tr><td colspan="7" class="text-center text-muted-c py-3">Belum ada riwayat audit trail.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
