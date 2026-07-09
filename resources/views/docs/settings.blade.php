@extends('docs.layouts.app')

@section('title', 'Settings')

@php $activeMenu = 'settings' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Settings</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Settings</span></div>
        </div>
      </div>

      <div class="row g-3">
        <div class="col-lg-3">
          <div class="card">
            <div class="card-body">
              <ul class="list-unstyled m-0 settings-menu">
                <li class="nav-item active"><a href="#" class="nav-link" data-settings-target="#settings-account"><i class="bi bi-person-fill"></i><span class="nav-label-text">Akun</span></a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-settings-target="#settings-security"><i class="bi bi-shield-lock-fill"></i><span class="nav-label-text">Keamanan</span></a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-settings-target="#settings-notifications"><i class="bi bi-bell-fill"></i><span class="nav-label-text">Notifikasi</span></a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-settings-target="#settings-appearance"><i class="bi bi-palette-fill"></i><span class="nav-label-text">Tampilan</span></a></li>
                <li class="nav-item"><a href="#" class="nav-link" data-settings-target="#settings-apikeys"><i class="bi bi-key-fill"></i><span class="nav-label-text">API Keys</span></a></li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-9">
          <div class="settings-panels">
            <div id="settings-account" class="settings-panel">
              <div class="card mb-3">
                <div class="card-header-flex"><h6>Preferensi Tampilan</h6></div>
                <div class="card-body">
                  <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid var(--border-subtle);">
                    <div>
                      <div style="font-size:0.88rem; font-weight:500;">Mode Gelap</div>
                      <div class="text-muted-c" style="font-size:0.78rem;">Gunakan tampilan gelap di seluruh dashboard</div>
                    </div>
                    <label class="switch-modern"><input type="checkbox" id="darkModeSwitch" checked><span class="switch-track"></span></label>
                  </div>
                  <div class="d-flex align-items-center justify-content-between py-2" style="border-bottom:1px solid var(--border-subtle);">
                    <div>
                      <div style="font-size:0.88rem; font-weight:500;">Sidebar Ringkas</div>
                      <div class="text-muted-c" style="font-size:0.78rem;">Tampilkan sidebar dalam mode terlipat secara default</div>
                    </div>
                    <label class="switch-modern"><input type="checkbox" id="sidebarCompactSwitch"><span class="switch-track"></span></label>
                  </div>
                  <div class="d-flex align-items-center justify-content-between py-2">
                    <div>
                      <div style="font-size:0.88rem; font-weight:500;">Animasi Antarmuka</div>
                      <div class="text-muted-c" style="font-size:0.78rem;">Aktifkan transisi dan animasi halus</div>
                    </div>
                    <label class="switch-modern"><input type="checkbox" checked><span class="switch-track"></span></label>
                  </div>
                </div>
              </div>

              <div class="card">
                <div class="card-header-flex"><h6>Notifikasi Email</h6></div>
                <div class="card-body">
                  <div class="form-check-modern mb-3"><input type="checkbox" id="n1" checked><label for="n1" class="text-secondary-c" style="font-size:0.85rem;">Laporan mingguan</label></div>
                  <div class="form-check-modern mb-3"><input type="checkbox" id="n2" checked><label for="n2" class="text-secondary-c" style="font-size:0.85rem;">Peringatan keamanan</label></div>
                  <div class="form-check-modern mb-3"><input type="checkbox" id="n3"><label for="n3" class="text-secondary-c" style="font-size:0.85rem;">Promosi &amp; penawaran</label></div>
                  <button class="btn btn-primary-grad mt-2" onclick="NexoraToast('Pengaturan berhasil disimpan.', 'success')">Simpan Pengaturan</button>
                </div>
              </div>
            </div>

            <div id="settings-security" class="settings-panel" style="display:none;">
              <div class="card">
                <div class="card-header-flex"><h6>Keamanan Akun</h6></div>
                <div class="card-body">
                  <p class="text-secondary-c">Atur autentikasi dua faktor, riwayat login, dan sesi aktif.</p>
                  <button class="btn btn-outline-soft">Kelola MFA</button>
                </div>
              </div>
            </div>

            <div id="settings-notifications" class="settings-panel" style="display:none;">
              <div class="card">
                <div class="card-header-flex"><h6>Notifikasi</h6></div>
                <div class="card-body">
                  <p class="text-secondary-c">Konfigurasi pemberitahuan aplikasi dan email.</p>
                </div>
              </div>
            </div>

            <div id="settings-appearance" class="settings-panel" style="display:none;">
              <div class="card">
                <div class="card-header-flex"><h6>Tampilan</h6></div>
                <div class="card-body">
                  <p class="text-secondary-c">Tema, warna, dan layout sidebar.</p>
                </div>
              </div>
            </div>

            <div id="settings-apikeys" class="settings-panel" style="display:none;">
              <div class="card">
                <div class="card-header-flex"><h6>API Keys</h6></div>
                <div class="card-body">
                  <p class="text-secondary-c">Buat, cabut, dan kelola kunci API aplikasi Anda.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection
