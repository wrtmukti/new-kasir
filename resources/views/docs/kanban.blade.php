@extends('docs.layouts.app')

@section('title', 'Kanban')

@php $activeMenu = 'kanban' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Kanban Board</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Kanban</span></div>
        </div>
        <button class="btn btn-primary-grad"><i class="bi bi-plus-lg me-1"></i>Tambah Task</button>
      </div>

      <div class="row g-3">

        <!-- To Do -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header-flex">
              <h6><i class="bi bi-circle me-1" style="color:var(--text-muted);"></i> To Do</h6>
              <span class="badge badge-primary badge-pill">3</span>
            </div>
            <div class="card-body" style="min-height:260px;">
              <div class="card card-hover-lift p-3 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 style="font-size:0.82rem;">Desain Halaman Login</h6>
                  <span class="tag tag-primary">desain</span>
                </div>
                <p class="text-muted-c mb-0" style="font-size:0.78rem;">Membuat mockup Figma untuk halaman login baru.</p>
              </div>
              <div class="card card-hover-lift p-3 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 style="font-size:0.82rem;">Integrasi API Payment</h6>
                  <span class="tag tag-danger">backend</span>
                </div>
                <p class="text-muted-c mb-0" style="font-size:0.78rem;">Menghubungkan Stripe untuk pembayaran otomatis.</p>
              </div>
              <div class="card card-hover-lift p-3">
                <div class="d-flex justify-content-between align-items-start">
                  <h6 style="font-size:0.82rem;">User Onboarding Flow</h6>
                  <span class="tag">UX</span>
                </div>
                <p class="text-muted-c mb-0" style="font-size:0.78rem;">Wireframe alur onboarding untuk pengguna baru.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- In Progress -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header-flex">
              <h6><i class="bi bi-arrow-repeat me-1" style="color:var(--warning);"></i> In Progress</h6>
              <span class="badge badge-warning badge-pill">2</span>
            </div>
            <div class="card-body" style="min-height:260px;">
              <div class="card card-hover-lift p-3 mb-2" style="border-left:3px solid var(--accent-1);">
                <h6 style="font-size:0.82rem;">Dashboard Analytics</h6>
                <p class="text-muted-c mb-0" style="font-size:0.78rem;">Membangun chart dan widget untuk halaman analytics.</p>
              </div>
              <div class="card card-hover-lift p-3" style="border-left:3px solid var(--warning);">
                <h6 style="font-size:0.82rem;">Database Optimization</h6>
                <p class="text-muted-c mb-0" style="font-size:0.78rem;">Optimasi query lambat dan indexing.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Done -->
        <div class="col-md-4">
          <div class="card">
            <div class="card-header-flex">
              <h6><i class="bi bi-check-circle me-1" style="color:var(--success);"></i> Done</h6>
              <span class="badge badge-success badge-pill">2</span>
            </div>
            <div class="card-body" style="min-height:260px;">
              <div class="card card-hover-lift p-3 mb-2" style="opacity:0.75;">
                <h6 style="font-size:0.82rem;">Setup CI/CD Pipeline</h6>
                <p class="text-muted-c mb-0" style="font-size:0.78rem;">GitHub Actions untuk auto-deploy ke staging.</p>
              </div>
              <div class="card card-hover-lift p-3" style="opacity:0.75;">
                <h6 style="font-size:0.82rem;">API Documentation</h6>
                <p class="text-muted-c mb-0" style="font-size:0.78rem;">Swagger docs untuk semua endpoint REST.</p>
              </div>
            </div>
          </div>
        </div>

      </div>
@endsection
