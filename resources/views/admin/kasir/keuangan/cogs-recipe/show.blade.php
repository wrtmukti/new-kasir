@extends('admin.layouts.app')

@section('title', 'Detail Resep Standar HPP')

@php $activeMenu = 'cogs-recipe' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Resep: {{ $cogsRecipe->recipe_name }}</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.keuangan.cogs-recipe.index') }}">Resep & COGS Menu</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Detail</span>
    </div>
  </div>
  <div>
    <a href="{{ route('admin.keuangan.cogs-recipe.edit', $cogsRecipe) }}" class="btn btn-outline-soft me-2"><i class="bi bi-pencil me-1"></i>Edit Resep</a>
    <a href="{{ route('admin.keuangan.cogs-recipe.index') }}" class="btn btn-outline-soft"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>

<div class="row g-4">
  <div class="col-md-4">
    <div class="card mb-4">
      <div class="card-header-flex">
        <h6>Ringkasan Estimasi Modal</h6>
      </div>
      <div class="card-body p-0">
        <table class="detail-table">
          <tr><td class="detail-label">Nama Resep</td><td class="detail-value fw-bold">{{ $cogsRecipe->recipe_name }}</td></tr>
          <tr>
            <td class="detail-label">Menu Kasir</td>
            <td class="detail-value">
              @if($cogsRecipe->product)
                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: var(--accent-1);">{{ $cogsRecipe->product->product_name }}</span>
              @else
                <span class="chip-tag">Independen</span>
              @endif
            </td>
          </tr>
          <tr><td class="detail-label">Target Food Cost</td><td class="detail-value fw-bold text-info">{{ number_format($cogsRecipe->target_food_cost, 1) }}%</td></tr>
          <tr><td class="detail-label">Estimasi COGS (Modal)</td><td class="detail-value fw-bold text-danger fs-5">Rp {{ number_format($cogsRecipe->estimated_cogs, 2, ',', '.') }}</td></tr>
          <tr><td class="detail-label">Saran Harga Jual</td><td class="detail-value fw-bold text-success fs-5">Rp {{ number_format($cogsRecipe->suggested_price, 0, ',', '.') }}</td></tr>
        </table>
      </div>
    </div>

    @if($cogsRecipe->notes)
    <div class="card">
      <div class="card-header-flex">
        <h6>Catatan Instruksi</h6>
      </div>
      <div class="card-body p-3">
        <p class="mb-0 text-muted-c" style="font-size: 0.9rem;">{{ $cogsRecipe->notes }}</p>
      </div>
    </div>
    @endif
  </div>

  <div class="col-md-8">
    <div class="card mb-4">
      <div class="card-header-flex">
        <h6>Rincian Takaran Bahan Penyusun</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern">
            <thead>
              <tr>
                <th class="ps-3">Bahan Mentah</th>
                <th>Harga Efektif</th>
                <th>Takaran / Qty</th>
                <th class="text-end pe-3">Subtotal Modal</th>
              </tr>
            </thead>
            <tbody>
              @foreach($cogsRecipe->items as $item)
              <tr>
                <td class="ps-3 fw-bold" style="color: var(--text-primary);">{{ $item->rawMaterial->name ?? 'Bahan Terhapus' }}</td>
                <td style="color: var(--text-secondary);">Rp {{ number_format($item->rawMaterial->effective_price ?? 0, 2) }} / {{ $item->rawMaterial->unit ?? '-' }}</td>
                <td><span class="chip-tag">{{ number_format($item->ingredient_qty, 4) }} {{ $item->rawMaterial->unit ?? '' }}</span></td>
                <td class="text-end pe-3 fw-bold text-danger">Rp {{ number_format($item->ingredient_cost, 2, ',', '.') }}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="fw-bold" style="background: var(--bg-elevated);">
                <td colspan="3" class="ps-3 text-end" style="color: var(--text-primary);">TOTAL ESTIMASI COGS (MODAL IDEAL):</td>
                <td class="text-end pe-3 text-danger fs-6">Rp {{ number_format($cogsRecipe->estimated_cogs, 2, ',', '.') }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- Audit Trail History -->
    <div class="card">
      <div class="card-header-flex">
        <h6>Audit Trail / Riwayat Perubahan Resep</h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table-modern" style="font-size: 0.88rem;">
            <thead>
              <tr>
                <th class="ps-3">Tanggal</th>
                <th>Aksi</th>
                <th class="text-center">Target FC %</th>
                <th class="text-end">Estimasi COGS</th>
                <th>Oleh</th>
                <th class="pe-3">Catatan</th>
              </tr>
            </thead>
            <tbody>
              @forelse($cogsRecipe->histories->sortByDesc('created_at') as $h)
              <tr>
                <td class="ps-3" style="color: var(--text-secondary);">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  @if($h->action_type == 'create')
                    <span class="badge" style="background: rgba(52, 211, 153, 0.15); color: var(--success);">Create</span>
                  @elseif($h->action_type == 'update')
                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: var(--accent-1);">Update</span>
                  @elseif($h->action_type == 'delete')
                    <span class="badge" style="background: rgba(248, 113, 113, 0.15); color: var(--danger);">Delete</span>
                  @else
                    <span class="badge" style="background: var(--bg-elevated-2); color: var(--text-secondary);">{{ $h->action_type }}</span>
                  @endif
                </td>
                <td class="text-center" style="color: var(--text-secondary);">{{ number_format($h->target_food_cost, 1) }}%</td>
                <td class="text-end fw-bold text-danger">Rp {{ number_format($h->estimated_cogs, 2, ',', '.') }}</td>
                <td style="color: var(--text-secondary);">{{ $h->changed_by ?? 'Admin' }}</td>
                <td class="pe-3"><small class="text-muted-c">{{ $h->history_remark }}</small></td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center text-muted-c py-3">Belum ada riwayat audit trail resep.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
