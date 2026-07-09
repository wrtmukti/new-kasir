@extends('docs.layouts.app')

@section('title', 'Buttons')

@php $activeMenu = 'buttons' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Buttons</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><a href="{{ url('docs/components') }}">UI Components</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Buttons</span></div>
        </div>
      </div>

      <!-- Color Variants -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Warna</h6></div>
        <div class="card-body d-flex flex-wrap gap-2">
          <button class="btn btn-primary-grad"><i class="bi bi-lightning-fill"></i> Primary</button>
          <button class="btn btn-success"><i class="bi bi-check-circle"></i> Success</button>
          <button class="btn btn-danger-grad"><i class="bi bi-trash"></i> Danger</button>
          <button class="btn btn-warning"><i class="bi bi-exclamation-triangle"></i> Warning</button>
          <button class="btn btn-info"><i class="bi bi-info-circle"></i> Info</button>
          <button class="btn btn-outline-soft"><i class="bi bi-box-arrow-in-right"></i> Outline</button>
          <button class="btn btn-ghost"><i class="bi bi-three-dots"></i> Ghost</button>
        </div>
      </div>

      <!-- Sizes -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Ukuran</h6></div>
        <div class="card-body d-flex flex-wrap gap-2 align-items-center">
          <button class="btn btn-primary-grad btn-sm">Small</button>
          <button class="btn btn-primary-grad">Default</button>
          <button class="btn btn-primary-grad btn-lg">Large</button>
          <button class="btn btn-outline-soft btn-icon-sq btn-sm"><i class="bi bi-search"></i></button>
          <button class="btn btn-outline-soft btn-icon-sq"><i class="bi bi-search"></i></button>
          <button class="btn btn-outline-soft btn-icon-lg"><i class="bi bi-search"></i></button>
          <button class="btn btn-outline-soft btn-icon-sq btn-icon-circle"><i class="bi bi-arrow-right"></i></button>
        </div>
      </div>

      <!-- States -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>State</h6></div>
        <div class="card-body d-flex flex-wrap gap-2">
          <button class="btn btn-primary-grad">Normal</button>
          <button class="btn btn-primary-grad" disabled>Disabled</button>
          <button class="btn btn-outline-soft" disabled>Disabled</button>
          <button class="btn btn-ghost" disabled>Disabled</button>
          <button class="btn btn-primary-grad"><span class="spinner spinner-sm" style="border-top-color:#fff;border-color:rgba(255,255,255,0.3);"></span> Loading</button>
          <button class="btn btn-outline-soft"><i class="bi bi-check-circle-fill" style="color:var(--success);"></i> Saved</button>
        </div>
      </div>

      <!-- Button Group -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Button Group</h6></div>
        <div class="card-body">
          <div class="btn-group mb-2">
            <button class="btn btn-outline-soft"><i class="bi bi-align-start"></i></button>
            <button class="btn btn-outline-soft"><i class="bi bi-align-center"></i></button>
            <button class="btn btn-outline-soft"><i class="bi bi-align-end"></i></button>
            <button class="btn btn-outline-soft"><i class="bi bi-justify"></i></button>
          </div>
          <br>
          <div class="btn-group">
            <button class="btn btn-primary-grad">Save</button>
            <button class="btn btn-primary-grad"><i class="bi bi-chevron-down"></i></button>
          </div>
        </div>
      </div>

      <!-- Block / Full Width -->
      <div class="card mb-3">
        <div class="card-header-flex"><h6>Block / Full Width</h6></div>
        <div class="card-body">
          <button class="btn btn-primary-grad btn-block mb-2">Tombol Penuh</button>
          <button class="btn btn-outline-soft btn-block"><i class="bi bi-download"></i> Unduh Laporan</button>
        </div>
      </div>
@endsection
