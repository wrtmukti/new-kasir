@extends('admin.layouts.app')

@section('title', 'Tambah Stok')

@php $activeMenu = 'stock' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Stok</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.stock.index') }}">Stok</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-box-seam me-2"></i>Informasi Stok</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.stock.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label-modern">Perusahaan</label>
          <select name="company_id" class="form-select-modern">
            <option value="">-- Pilih Perusahaan --</option>
            @foreach($companies as $c)
              <option value="{{ $c->company_id }}" {{ old('company_id') == $c->company_id ? 'selected' : '' }}>{{ $c->company_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Kode Stok</label>
          <input type="text" name="stock_code" class="form-control-modern" value="{{ old('stock_code') }}" placeholder="STK-001">
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nama Stok <span class="text-danger">*</span></label>
          <input type="text" name="stock_name" class="form-control-modern @error('stock_name') is-invalid @enderror" value="{{ old('stock_name') }}" placeholder="Nama bahan baku">
          @error('stock_name')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Deskripsi</label>
          <textarea name="stock_description" class="form-control-modern" rows="3" placeholder="Deskripsi">{{ old('stock_description') }}</textarea>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Tipe</label>
          <input type="text" name="stock_type" class="form-control-modern" value="{{ old('stock_type') }}" placeholder="bahan baku">
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Unit</label>
          <select name="stock_unit" class="form-select-modern">
            <option value="">-- Pilih --</option>
            <option value="pcs" {{ old('stock_unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
            <option value="kg" {{ old('stock_unit') == 'kg' ? 'selected' : '' }}>Kg</option>
            <option value="gr" {{ old('stock_unit') == 'gr' ? 'selected' : '' }}>Gr</option>
            <option value="liter" {{ old('stock_unit') == 'liter' ? 'selected' : '' }}>Liter</option>
            <option value="ml" {{ old('stock_unit') == 'ml' ? 'selected' : '' }}>Ml</option>
            <option value="box" {{ old('stock_unit') == 'box' ? 'selected' : '' }}>Box</option>
            <option value="pack" {{ old('stock_unit') == 'pack' ? 'selected' : '' }}>Pack</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Jumlah Stok</label>
          <input type="number" name="stock_amount" class="form-control-modern" value="{{ old('stock_amount', 0) }}" min="0">
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Harga Satuan</label>
          <input type="number" name="stock_price" class="form-control-modern" value="{{ old('stock_price') }}" min="0" step="0.01" placeholder="0">
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Status</label>
          <select name="stock_status" class="form-select-modern">
            <option value="1" {{ old('stock_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('stock_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad">Simpan</button>
          <a href="{{ route('admin.stock.index') }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
