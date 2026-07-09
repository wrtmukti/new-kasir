@extends('docs.layouts.app')

@section('title', 'Blank')

@php $activeMenu = 'blank' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Blank Page</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Blank</span></div>
        </div>
      </div>

      <div class="card">
        <div class="card-body empty-state" style="padding:5rem 1.5rem;">
          <i class="bi bi-file-earmark-plus"></i>
          <h5 class="mt-2">Mulai Pengembangan di Sini</h5>
          <p class="text-muted-c" style="max-width:480px; margin:0 auto;">
            Halaman kosong ini bisa Anda gunakan sebagai titik awal untuk halaman baru.
            Salin struktur <code>&lt;main class="page-content"&gt;</code> dan mulailah menambahkan konten Anda sendiri.
          </p>
        </div>
      </div>
@endsection
