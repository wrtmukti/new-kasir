@extends('docs.layouts.app')

@section('title', 'Profile')

@php $activeMenu = 'profile' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Profile</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Profile</span></div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card text-center">
            <div class="card-body">
              <div class="user-avatar" style="margin:0 auto; width:72px; height:72px; font-size:1.4rem;">RA</div>
              <h5 class="mt-3 mb-1">Rangga A.</h5>
              <div class="text-muted-c" style="font-size:0.85rem;">Administrator</div>
              <div class="mt-3">
                <span class="pill pill-success"><i class="bi bi-check-circle"></i> Aktif</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header-flex"><h6>Edit Profil</h6></div>
            <div class="card-body">
              <form style="max-width:640px;">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label-modern">Nama Lengkap</label>
                    <input class="form-control-modern" value="Rangga A.">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Email</label>
                    <input class="form-control-modern" value="rangga@nexora.id">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Role</label>
                    <select class="form-select-modern"><option>Administrator</option><option>Editor</option></select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Nomor Telepon</label>
                    <input class="form-control-modern" placeholder="+62 812-xxxx-xxxx">
                  </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                  <button class="btn btn-primary-grad">Simpan Perubahan</button>
                  <button class="btn btn-outline-soft">Batal</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
@endsection
