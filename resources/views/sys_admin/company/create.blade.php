@extends('sys_admin.layouts.app')

@section('title', 'Tambah Perusahaan')

@php $activeMenu = 'outlet' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Tambah Perusahaan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('sys_admin.company.index') }}">Perusahaan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Tambah</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-building me-2"></i>Informasi Perusahaan</h6></div>
  <div class="card-body">
    <form action="{{ route('sys_admin.company.store') }}" method="POST">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label-modern">Nama Perusahaan <span class="text-danger">*</span></label>
          <input type="text" name="outlet_name" class="form-control-modern @error('outlet_name') is-invalid @enderror" value="{{ old('outlet_name') }}" placeholder="Masukkan nama perusahaan">
          @error('outlet_name')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Kode Perusahaan</label>
          <input type="text" name="outlet_code" class="form-control-modern @error('outlet_code') is-invalid @enderror" value="{{ old('outlet_code') }}" placeholder="GGB">
          @error('outlet_code')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Cabang</label>
          <input type="text" name="outlet_branch" class="form-control-modern" value="{{ old('outlet_branch') }}" placeholder="Jakarta">
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Email</label>
          <input type="email" name="outlet_email" class="form-control-modern" value="{{ old('outlet_email') }}" placeholder="email@perusahaan.com">
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Telepon</label>
          <input type="text" name="outlet_phone" class="form-control-modern" value="{{ old('outlet_phone') }}" placeholder="021-xxxxxxx">
        </div>
        <div class="col-12">
          <label class="form-label-modern">Alamat</label>
          <textarea name="outlet_address" class="form-control-modern" rows="3" placeholder="Alamat lengkap">{{ old('outlet_address') }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Status</label>
          <select name="outlet_status" class="form-select-modern">
            <option value="1" {{ old('outlet_status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('outlet_status') === '0' ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad">Simpan</button>
          <a href="{{ route('sys_admin.company.index') }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
