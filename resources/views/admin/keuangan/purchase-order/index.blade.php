@extends('admin.layouts.app')

@section('title', 'Purchase Order Bahan Mentah')

@php $activeMenu = 'purchase-order' @endphp

@section('content')
<main class="page-content">
  <div class="page-header">
    <div>
      <h1>Purchase Order (PO Bahan Mentah)</h1>
      <div class="breadcrumb-trail">
        <a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
        <span>Keuangan</span><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i>
        <span>Purchase Order</span>
      </div>
    </div>
    <div>
      <a href="{{ route('admin.keuangan.purchase-order.create') }}" class="btn btn-primary-grad">
        <i class="bi bi-plus-lg me-1"></i>Buat PO Baru
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-header-flex">
      <h6><i class="bi bi-list-columns-reverse me-2"></i>Daftar Purchase Order (PO) Bahan Mentah</h6>
      <span class="chip-tag">Terhubung COGS / HPP</span>
    </div>
    <div class="card-body p-0">
      <div id="tableContainer">
        @include('admin.keuangan.purchase-order._data')
      </div>
    </div>
  </div>
</main>
@endsection
