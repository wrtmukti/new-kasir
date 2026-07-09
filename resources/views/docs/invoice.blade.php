@extends('docs.layouts.app')

@section('title', 'Invoice')

@php $activeMenu = 'invoice' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Invoice</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Invoice</span></div>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-soft"><i class="bi bi-printer me-1"></i>Cetak</button>
          <button class="btn btn-primary-grad"><i class="bi bi-download me-1"></i>Unduh PDF</button>
        </div>
      </div>

      <div class="card" style="max-width:820px;">
        <div class="card-body">

          <!-- Invoice Header -->
          <div class="d-flex justify-content-between align-items-start flex-wrap mb-4">
            <div>
              <h5>PT. Nexora Teknologi</h5>
              <div class="text-muted-c" style="font-size:0.84rem;">
                Jl. Inovasi No. 88, Bandung<br>
                info@nexora.id &bull; +62 22 1234 5678
              </div>
            </div>
            <div class="text-end">
              <div class="brand-mark" style="margin:0 0 0.5rem auto;">N</div>
              <strong>INV-2026-001</strong><br>
              <span class="text-muted-c" style="font-size:0.84rem;">Tanggal: 21 Juni 2026</span>
            </div>
          </div>

          <!-- Bill To -->
          <div class="mb-4">
            <span class="badge badge-primary badge-pill mb-2">Kepada</span>
            <h6 class="mb-1">CV. Mitra Digital</h6>
            <div class="text-muted-c" style="font-size:0.84rem;">
              Jl. Merdeka No. 45, Jakarta<br>
              mitra@email.com
            </div>
          </div>

          <!-- Tabel Item -->
          <div class="table-responsive mb-3">
            <table class="table-modern">
              <thead>
                <tr>
                  <th style="width:50%;">Deskripsi</th>
                  <th>Qty</th>
                  <th>Harga Satuan</th>
                  <th style="text-align:right;">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="cell-primary">Paket Pro — Langganan Bulanan</div>
                    <div class="text-muted-c" style="font-size:0.78rem;">Akses penuh ke semua fitur, API calls 10.000/hari</div>
                  </td>
                  <td>1</td>
                  <td class="text-mono">$49.00</td>
                  <td class="text-mono" style="text-align:right;">$49.00</td>
                </tr>
                <tr>
                  <td>
                    <div class="cell-primary">Domain Kustom</div>
                    <div class="text-muted-c" style="font-size:0.78rem;">Hosting domain kustom untuk white-label</div>
                  </td>
                  <td>1</td>
                  <td class="text-mono">$15.00</td>
                  <td class="text-mono" style="text-align:right;">$15.00</td>
                </tr>
                <tr>
                  <td>
                    <div class="cell-primary">Storage Tambahan 50GB</div>
                    <div class="text-muted-c" style="font-size:0.78rem;">Melebihi kapasitas storage standar</div>
                  </td>
                  <td>1</td>
                  <td class="text-mono">$10.00</td>
                  <td class="text-mono" style="text-align:right;">$10.00</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Total & Payment Info -->
          <div class="row g-3">
            <div class="col-md-6">
              <div class="text-muted-c" style="font-size:0.84rem;">
                <strong style="color:var(--text-primary);">Metode Pembayaran:</strong><br>
                Transfer Bank — BCA 1234567890<br>
                a.n. PT. Nexora Teknologi
              </div>
            </div>
            <div class="col-md-6">
              <div class="card bg-elevated p-3" style="text-align:right; background:var(--bg-elevated);">
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted-c">Subtotal</span>
                  <span class="text-mono">$74.00</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted-c">PPN (11%)</span>
                  <span class="text-mono">$8.14</span>
                </div>
                <hr class="divider-line my-2">
                <div class="d-flex justify-content-between">
                  <strong>Total</strong>
                  <span class="text-mono" style="font-size:1.15rem; color:var(--accent-1);">$82.14</span>
                </div>
              </div>
            </div>
          </div>

          <div class="divider-label mt-3">SYARAT PEMBAYARAN</div>
          <p class="text-muted-c mt-2 mb-0" style="font-size:0.82rem;">
            Pembayaran harus dilunasi dalam waktu 30 hari sejak tanggal invoice. Denda keterlambatan 2% per bulan.
          </p>

        </div>
      </div>
@endsection
