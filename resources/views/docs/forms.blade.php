@extends('docs.layouts.app')

@section('title', 'Forms')

@php $activeMenu = 'forms' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1><i class="bi bi-ui-checks me-2"></i>Forms</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><a href="{{ url('docs/components') }}">UI Components</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Forms</span></div>
        </div>
      </div>

      {{-- ================ PROFILE FORM ================ --}}
      <div class="row g-3">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-header-flex"><h6><i class="bi bi-person-circle me-2"></i>Informasi Profil</h6></div>
            <div class="card-body">
              <form onsubmit="event.preventDefault(); alert('Profil berhasil disimpan.');">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label-modern">Nama Lengkap</label>
                    <input type="text" class="form-control-modern" placeholder="Masukkan nama lengkap" value="Rangga Aditya">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Email</label>
                    <input type="email" class="form-control-modern" placeholder="nama@email.com" value="rangga.aditya@test.id">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Peran</label>
                    <select class="form-select-modern">
                      <option>Administrator</option>
                      <option>Editor</option>
                      <option>Viewer</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Nomor Telepon</label>
                    <input type="text" class="form-control-modern" placeholder="+62 812-xxxx-xxxx">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Departemen</label>
                    <select class="form-select-modern">
                      <option>-- Pilih Dept --</option>
                      <optgroup label="Production">
                        <option>MB/MS</option>
                        <option>Milling</option>
                        <option>Timing</option>
                      </optgroup>
                      <optgroup label="Support">
                        <option>IT</option>
                        <option>QC</option>
                      </optgroup>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label-modern">Upload Foto</label>
                    <input type="file" class="form-control-modern">
                  </div>
                  <div class="col-12">
                    <label class="form-label-modern">Bio</label>
                    <textarea class="form-control-modern" rows="3" placeholder="Tulis sedikit tentang diri Anda..."></textarea>
                  </div>
                  <div class="col-12">
                    <hr class="divider-line">
                    <div class="form-check-modern mb-2">
                      <input type="checkbox" id="notifEmail" checked>
                      <label for="notifEmail" class="text-secondary-c" style="font-size:0.85rem;">Kirim notifikasi lewat email</label>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="max-width:320px;">
                      <span class="text-secondary-c" style="font-size:0.85rem;">Mode Tersedia</span>
                      <label class="switch-modern">
                        <input type="checkbox" checked>
                        <span class="switch-track"></span>
                      </label>
                    </div>
                  </div>
                  <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary-grad">Simpan Perubahan</button>
                    <button type="button" class="btn btn-outline-soft">Batal</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card mb-3">
            <div class="card-header-flex"><h6><i class="bi bi-dot-circle me-2"></i>Radio &amp; Checkbox</h6></div>
            <div class="card-body">
              <label class="form-label-modern">Paket Berlangganan</label>
              <div class="form-check-modern mb-2"><input type="radio" name="plan" checked> <label class="text-secondary-c" style="font-size:0.85rem;">Starter — Gratis</label></div>
              <div class="form-check-modern mb-2"><input type="radio" name="plan"> <label class="text-secondary-c" style="font-size:0.85rem;">Pro — $49/bulan</label></div>
              <div class="form-check-modern"><input type="radio" name="plan"> <label class="text-secondary-c" style="font-size:0.85rem;">Team — $129/bulan</label></div>
            </div>
          </div>

          <div class="card mb-3">
            <div class="card-header-flex"><h6><i class="bi bi-select-all me-2"></i>Select &amp; File</h6></div>
            <div class="card-body d-flex flex-column gap-3">
              <div>
                <label class="form-label-modern">Default Select</label>
                <select class="form-select-modern">
                  <option>-- Pilih Opsi --</option>
                  <option>Option 1</option>
                  <option>Option 2</option>
                  <option>Option 3</option>
                </select>
              </div>
              <div>
                <label class="form-label-modern">Disabled Select</label>
                <select class="form-select-modern" disabled style="opacity:0.5;">
                  <option>Tidak tersedia</option>
                </select>
              </div>
              <div>
                <label class="form-label-modern">Upload File</label>
                <input type="file" class="form-control-modern">
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header-flex"><h6><i class="bi bi-search me-2"></i>Pencarian &amp; Filter</h6></div>
            <div class="card-body">
              <label class="form-label-modern">Cari Pengguna</label>
              <div class="topbar-search mb-3" style="max-width:100%;">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control-modern" style="padding-left:2.3rem;" placeholder="Ketik nama pengguna...">
              </div>
              <label class="form-label-modern">Rentang Tanggal</label>
              <input type="date" class="form-control-modern">
              <label class="form-label-modern mt-3">Upload File</label>
              <input type="file" class="form-control-modern">
            </div>
          </div>
        </div>
      </div>

      {{-- ================ INPUT SKELETON & BUTTON LOADING ================ --}}
      <div class="card mt-3">
        <div class="card-header-flex"><h6><i class="bi bi-magic me-2"></i>Input Skeleton &amp; Button Loading Demo</h6></div>
        <div class="card-body">
          <p class="text-muted-c mb-3" style="font-size:0.85rem;">
            Bungkus input dengan <code>.input-skeleton</code> — saat form di-submit, input jadi shimmer.
            Tambah class <code>.btn-loading</code> ke tombol — saat diklik jadi loading.
          </p>
          <form onsubmit="event.preventDefault(); var f=this; setTimeout(function(){ alert('Simulasi submit berhasil'); f.querySelectorAll('.input-skeleton').forEach(function(e){e.classList.remove('is-loading');}); }, 800);" style="max-width:500px;">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label-modern">Nama Lengkap</label>
                <div class="input-skeleton">
                  <input type="text" class="form-control-modern" value="Rangga Aditya">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Email</label>
                <div class="input-skeleton">
                  <input type="email" class="form-control-modern" value="rangga@test.id">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Peran</label>
                <div class="input-skeleton">
                  <select class="form-select-modern">
                    <option>Administrator</option>
                    <option>Editor</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label-modern">Upload File</label>
                <div class="input-skeleton">
                  <input type="file" class="form-control-modern">
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-primary-grad btn-loading">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- ================ CODE SNIPPET ================ --}}
      <div class="card mt-3">
        <div class="card-header-flex"><h6><i class="bi bi-code-slash me-2"></i>Form Components</h6></div>
        <div class="card-body p-0">
          <div class="doc-code-wrap">
            <pre class="code-block mb-0" style="border-radius:0;border:none;"><code>&lt;!-- Single Select --&gt;
&lt;select class="form-select-modern"&gt;
  &lt;option&gt;Option 1&lt;/option&gt;
&lt;/select&gt;

&lt;!-- Input Skeleton (loading shimmer) --&gt;
&lt;div class="input-skeleton"&gt;
  &lt;input type="text" class="form-control-modern"&gt;
&lt;/div&gt;

&lt;!-- Button Loading --&gt;
&lt;button type="submit" class="btn btn-primary-grad btn-loading"&gt;Submit&lt;/button&gt;

&lt;!-- File input --&gt;
&lt;input type="file" class="form-control-modern"&gt;

&lt;!-- Checkbox --&gt;
&lt;div class="form-check-modern"&gt;
  &lt;input type="checkbox" id="x" checked&gt;
  &lt;label for="x"&gt;Label&lt;/label&gt;
&lt;/div&gt;

&lt;!-- Switch --&gt;
&lt;label class="switch-modern"&gt;
  &lt;input type="checkbox" checked&gt;
  &lt;span class="switch-track"&gt;&lt;/span&gt;
&lt;/label&gt;</code></pre>
          </div>
        </div>
      </div>
@endsection
