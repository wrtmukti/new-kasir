@extends('docs.layouts.app')

@section('title', 'Charts')

@php $activeMenu = 'charts' @endphp

@section('content')
      <div class="page-header">
        <div>
          <h1>Charts</h1>
          <div class="breadcrumb-trail"><a href="{{ url('docs/index') }}">Home</a><i class="bi bi-chevron-right" style="font-size:0.6rem;"></i><span>Charts</span></div>
        </div>
        <p class="text-muted-c mb-0">Koleksi chart modern berbasis Chart.js — 10 tipe siap pakai dengan palet warna gelap.</p>
      </div>

      <!-- Row 1: Line + Doughnut -->
      <div class="row g-3 mb-3">
        <div class="col-lg-8">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Line Chart — Tren Pendapatan</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Pendapatan bulanan dengan proyeksi 12 bulan</span>
              </div>
              <span class="status-dot live"></span>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartRevenueLine"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-header-flex">
              <h6>Doughnut — Penggunaan Model AI</h6>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartAIModels"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 2: Bar (vertical) + Horizontal Bar -->
      <div class="row g-3 mb-3">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Bar Chart — Request per Hari</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">7 hari terakhir</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartDailyRequests"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Horizontal Bar — Performa Tim</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Task selesai per anggota tim</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartTeamPerformance"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 3: Stacked Bar + Polar Area -->
      <div class="row g-3 mb-3">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Stacked Bar — Breakdown Biaya</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Per kuartal — Infrastructure, Engineering, Marketing</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartStackedCosts"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Polar Area — Sumber Trafik</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Berdasarkan channel akuisisi</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartTrafficSources"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 4: Radar + Mixed -->
      <div class="row g-3 mb-3">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Radar — Skor Produk</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Perbandingan fitur antar produk</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartProductRadar"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Mixed Chart — Revenue + Orders</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Bar: orders · Line: revenue</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartMixedRevenue"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 5: Bubble + Scatter -->
      <div class="row g-3 mb-3">
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Bubble Chart — Analisis Portofolio</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Risk vs Return (ukuran bubble = alokasi)</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartPortfolio"></canvas>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header-flex">
              <div>
                <h6>Scatter Chart — Korelasi Data</h6>
                <span class="text-muted-c" style="font-size:0.78rem;">Pengguna aktif vs engagement rate</span>
              </div>
            </div>
            <div class="card-body" style="height:280px;">
              <canvas id="chartScatter"></canvas>
            </div>
          </div>
        </div>
      </div>
@endsection
