@extends('docs.layouts.app')

@section('title', '404 Not Found')

@section('content')
      <div style="min-height:60vh; display:flex; align-items:center; justify-content:center;">
        <div style="text-align:center;">
          <h1 style="font-size:5rem; font-family:var(--font-display); background:var(--accent-gradient); -webkit-background-clip:text; background-clip:text; color:transparent; margin:0; line-height:1;">404</h1>
          <p class="text-secondary-c" style="font-size:1.1rem; margin:0.5rem 0 1.5rem;">Halaman yang Anda cari tidak ditemukan.</p>
          <a href="{{ url('docs/index') }}" class="btn btn-primary-grad"><i class="bi bi-arrow-left me-1"></i>Kembali ke Dashboard</a>
        </div>
      </div>
@endsection
