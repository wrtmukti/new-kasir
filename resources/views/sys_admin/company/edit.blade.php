@extends('sys_admin.layouts.app')

@section('title', 'Edit Perusahaan')

@php $activeMenu = 'company' @endphp

@section('content')
<div class="page-header">
  <div>
    <h1>Edit Perusahaan</h1>
    <div class="breadcrumb-trail">
      <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <a href="{{ route('sys_admin.company.index') }}">Perusahaan</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
      <span>Edit</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header-flex"><h6><i class="bi bi-building me-2"></i>Informasi Perusahaan</h6></div>
  <div class="card-body">
    <form action="{{ route('sys_admin.company.update', $company) }}" method="POST">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label-modern">Nama Perusahaan <span class="text-danger">*</span></label>
          <input type="text" name="company_name" class="form-control-modern @error('company_name') is-invalid @enderror" value="{{ old('company_name', $company->company_name) }}" placeholder="Masukkan nama perusahaan">
          @error('company_name')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Kode Perusahaan</label>
          <input type="text" name="company_code" class="form-control-modern" value="{{ old('company_code', $company->company_code) }}" placeholder="GGB">
        </div>
        <div class="col-md-3">
          <label class="form-label-modern">Cabang</label>
          <input type="text" name="company_branch" class="form-control-modern" value="{{ old('company_branch', $company->company_branch) }}" placeholder="Jakarta">
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Email</label>
          <input type="email" name="company_email" class="form-control-modern" value="{{ old('company_email', $company->company_email) }}" placeholder="email@perusahaan.com">
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Telepon</label>
          <input type="text" name="company_phone" class="form-control-modern" value="{{ old('company_phone', $company->company_phone) }}" placeholder="021-xxxxxxx">
        </div>
        <div class="col-12">
          <label class="form-label-modern">Alamat</label>
          <textarea name="company_address" class="form-control-modern" rows="3" placeholder="Alamat lengkap">{{ old('company_address', $company->company_address) }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label-modern">Status</label>
          <select name="company_status" class="form-select-modern">
            <option value="1" {{ old('company_status', $company->company_status) == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('company_status', $company->company_status) === '0' ? 'selected' : '' }}>Nonaktif</option>
          </select>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary-grad">Simpan Perubahan</button>
          <a href="{{ route('sys_admin.company.index') }}" class="btn btn-outline-soft">Batal</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection
