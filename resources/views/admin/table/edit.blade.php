@extends('admin.layouts.app')

@section('title', 'Edit Meja')

@php $activeMenu = 'table' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Edit Meja</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.table.index') }}">Meja</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Edit</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-grid-3x3-gap-fill me-2"></i>Informasi Meja</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.table.update', $table) }}" method="POST" class="form-submit-loading">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label-modern">Perusahaan</label>
          <div class="input-skeleton">
            <select name="company_id" class="form-select-modern">
              <option value="">-- Pilih Perusahaan --</option>
              @foreach($companies as $c)
                <option value="{{ $c->company_id }}" {{ old('company_id', $table->company_id) == $c->company_id ? 'selected' : '' }}>{{ $c->company_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nomor Meja <span class="text-danger">*</span></label>
          <div class="input-skeleton">
            <input type="number" name="table_number" class="form-control-modern @error('table_number') is-invalid @enderror" value="{{ old('table_number', $table->table_number) }}" placeholder="1" min="1">
            @error('table_number')
              <span class="text-danger d-block mt-1" style="font-size:0.85rem;">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Kapasitas</label>
          <div class="input-skeleton">
            <input type="number" name="table_capacity" class="form-control-modern" value="{{ old('table_capacity', $table->table_capacity) }}" min="1" placeholder="4">
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Deskripsi</label>
          <div class="input-skeleton">
            <textarea name="table_description" class="form-control-modern" rows="3" placeholder="Deskripsi meja">{{ old('table_description', $table->table_description) }}</textarea>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Status</label>
          <div class="input-skeleton">
            <select name="table_status" class="form-select-modern">
              <option value="active" {{ old('table_status', $table->table_status) == 'active' ? 'selected' : '' }}>Kosong</option>
              <option value="reserved" {{ old('table_status', $table->table_status) == 'reserved' ? 'selected' : '' }}>Dipesan</option>
              <option value="occupied" {{ old('table_status', $table->table_status) == 'occupied' ? 'selected' : '' }}>Terisi</option>
              <option value="inactive" {{ old('table_status', $table->table_status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad btn-loading">Simpan Perubahan</button>
          <a href="{{ route('admin.table.index') }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('.form-submit-loading');
  if (!form) return;

  form.addEventListener('submit', function(e) {
    e.preventDefault();

    form.querySelectorAll('.input-skeleton').forEach(function(el) {
      el.classList.add('is-loading');
    });
    const btn = form.querySelector('.btn-loading');
    if (btn) {
      btn.classList.add('is-loading');
      btn.disabled = true;
    }

    requestAnimationFrame(function() {
      setTimeout(function() {
        form.submit();
      }, 400);
    });
  });
});
</script>
@endpush
@endsection
