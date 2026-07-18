@extends('admin.layouts.app')

@section('title', 'Tambah Supplier')

@php $activeMenu = 'supplier' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Supplier</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('admin.supplier.index') }}">Supplier</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-truck me-2"></i>Informasi Supplier</h6></div>
  <div class="card-body">
    <form action="{{ route('admin.supplier.store') }}" method="POST">
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
          <label class="form-label-modern">Kode Supplier</label>
          <input type="text" name="supplier_code" class="form-control-modern" value="{{ old('supplier_code') }}" placeholder="SUP-001">
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Nama Supplier <span class="text-danger">*</span></label>
          <input type="text" name="supplier_name" class="form-control-modern @error('supplier_name') is-invalid @enderror" value="{{ old('supplier_name') }}" placeholder="Nama supplier">
          @error('supplier_name')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Kontak Person</label>
          <input type="text" name="supplier_contact" class="form-control-modern" value="{{ old('supplier_contact') }}" placeholder="Contact person">
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Telepon</label>
          <input type="text" name="supplier_phone" class="form-control-modern" value="{{ old('supplier_phone') }}" placeholder="021-xxxxxxx">
        </div>
        <div class="col-md-4">
          <label class="form-label-modern">Status</label>
          <select name="supplier_status" class="form-select-modern">
            <option value="1" {{ old('supplier_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('supplier_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label-modern">Alamat</label>
          <textarea name="supplier_address" class="form-control-modern" rows="3" placeholder="Alamat lengkap">{{ old('supplier_address') }}</textarea>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad">Simpan</button>
          <a href="{{ route('admin.supplier.index') }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
