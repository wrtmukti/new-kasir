@extends('docs.layouts.app')

@section('title', 'Users')

@php $activeMenu = 'users' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Users</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Users</span></div>
        </div>
        <button class="btn btn-primary-grad"><i class="bi bi-plus-lg me-1"></i>Tambah Data</button>
      </div>

      <div class="card">
        <div class="card-header-flex">
          <h6>Manajemen Pengguna</h6>
          <div class="d-flex gap-2">
            <select class="form-select-modern" style="width:auto;">
              <option>Semua Status</option>
              <option>Aktif</option>
              <option>Nonaktif</option>
            </select>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table-modern">
              <thead>
                <tr>
                  <th><input type="checkbox"></th>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Peran</th>
                  <th>Status</th>
                  <th>Bergabung</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="checkbox"></td>
                  <td class="cell-primary"><span class="avatar-sm">SA</span>Sinta Amalia</td>
                  <td>sinta.amalia@nexora.id</td>
                  <td><span class="chip-tag">Admin</span></td>
                  <td><span class="pill pill-success">Aktif</span></td>
                  <td class="text-muted-c">02 Jan 2026</td>
                  <td><button class="btn btn-ghost btn-icon-sq"><i class="bi bi-three-dots"></i></button></td>
                </tr>
                <tr>
                  <td><input type="checkbox"></td>
                  <td class="cell-primary"><span class="avatar-sm">BP</span>Budi Pratama</td>
                  <td>budi.pratama@nexora.id</td>
                  <td><span class="chip-tag">Editor</span></td>
                  <td><span class="pill pill-success">Aktif</span></td>
                  <td class="text-muted-c">14 Feb 2026</td>
                  <td><button class="btn btn-ghost btn-icon-sq"><i class="bi bi-three-dots"></i></button></td>
                </tr>
                <tr>
                  <td><input type="checkbox"></td>
                  <td class="cell-primary"><span class="avatar-sm">DK</span>Dewi Kartika</td>
                  <td>dewi.kartika@nexora.id</td>
                  <td><span class="chip-tag">Viewer</span></td>
                  <td><span class="pill pill-neutral">Nonaktif</span></td>
                  <td class="text-muted-c">28 Mar 2026</td>
                  <td><button class="btn btn-ghost btn-icon-sq"><i class="bi bi-three-dots"></i></button></td>
                </tr>
                <tr>
                  <td><input type="checkbox"></td>
                  <td class="cell-primary"><span class="avatar-sm">RH</span>Rian Hidayat</td>
                  <td>rian.hidayat@nexora.id</td>
                  <td><span class="chip-tag">Editor</span></td>
                  <td><span class="pill pill-warning">Pending</span></td>
                  <td class="text-muted-c">09 Apr 2026</td>
                  <td><button class="btn btn-ghost btn-icon-sq"><i class="bi bi-three-dots"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-header-flex" style="border-top:1px solid var(--border-subtle); border-bottom:none;">
          <span class="text-muted-c" style="font-size:0.8rem;">Menampilkan 1-4 dari 128 data</span>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-soft btn-sm">Sebelumnya</button>
            <button class="btn btn-outline-soft btn-sm">Selanjutnya</button>
          </div>
        </div>
      </div>
@endsection
