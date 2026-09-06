<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>X-Report Interim Kasir #{{ $dailyClosing->id }}</title>
  <style>
    body {
      font-family: 'Courier New', Courier, monospace;
      width: 80mm;
      margin: 0 auto;
      padding: 10px;
      background: #fff;
      color: #000;
      font-size: 12px;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .bold { font-weight: bold; }
    .divider { border-top: 1px dashed #000; margin: 8px 0; }
    .double-divider { border-top: 2px solid #000; margin: 8px 0; }
    .flex-between { display: flex; justify-content: space-between; }
    .badge-interim {
      border: 1px solid #000;
      padding: 3px 6px;
      display: inline-block;
      font-weight: bold;
      margin: 4px 0;
      font-size: 11px;
    }
    @media print {
      body { width: 100%; padding: 0; }
      @page { margin: 0; }
    }
  </style>
</head>
<body onload="window.print()">

  <div class="text-center">
    <div class="bold" style="font-size: 15px;">STRUK REKAP X-REPORT</div>
    <div class="badge-interim">*** INTERIM AUDIT (KASIR BERJALAN) ***</div>
    <div class="bold" style="font-size: 13px; margin-top: 4px;">{{ session('active_outlet_name') ?? 'KASIR POS' }}</div>
  </div>

  <div class="divider"></div>

  <div class="flex-between">
    <span>ID Sesi Kasir:</span>
    <span class="bold">#{{ $dailyClosing->id }}</span>
  </div>
  <div class="flex-between">
    <span>Tanggal Bisnis:</span>
    <span>{{ \Carbon\Carbon::parse($dailyClosing->business_date)->format('d/m/Y') }}</span>
  </div>
  <div class="flex-between">
    <span>Jam Buka Kasir:</span>
    <span class="bold">{{ $dailyClosing->shift_name }}</span>
  </div>
  <div class="flex-between">
    <span>Kasir Bertugas:</span>
    <span>{{ $dailyClosing->cashier?->name ?? 'ID #' . $dailyClosing->cashier_id }}</span>
  </div>

  <div class="divider"></div>

  <div class="flex-between">
    <span>Waktu Buka Kasir:</span>
    <span>{{ \Carbon\Carbon::parse($dailyClosing->opened_at)->format('d/m/Y H:i') }}</span>
  </div>
  <div class="flex-between">
    <span>Jam Audit X-Report:</span>
    <span class="bold">{{ now()->format('d/m/Y H:i:s') }}</span>
  </div>
  <div class="flex-between">
    <span>Durasi Kasir Buka:</span>
    <span>{{ \Carbon\Carbon::parse($dailyClosing->opened_at)->diffForHumans(null, true) }}</span>
  </div>

  <div class="double-divider"></div>
  <div class="bold text-center">RINGKASAN PENJUALAN KASIR</div>
  <div class="double-divider"></div>

  <div class="flex-between">
    <span>Total Transaksi Order:</span>
    <span>{{ $orders->count() }} Pesanan</span>
  </div>
  <div class="flex-between">
    <span>Penjualan Tunai (Cash):</span>
    <span>Rp {{ number_format($cashSales, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between">
    <span>Penjualan Non-Tunai (QRIS/EDC):</span>
    <span>Rp {{ number_format($nonCashSales, 0, ',', '.') }}</span>
  </div>
  <div class="divider"></div>
  <div class="flex-between bold" style="font-size: 13px;">
    <span>TOTAL OMZET SEMENTARA:</span>
    <span>Rp {{ number_format($cashSales + $nonCashSales, 0, ',', '.') }}</span>
  </div>

  <div class="double-divider"></div>
  <div class="bold text-center">RINCIAN KAS & LACI (DRAWER)</div>
  <div class="double-divider"></div>

  <div class="flex-between">
    <span>Modal Awal Kasir:</span>
    <span>Rp {{ number_format($dailyClosing->starting_cash, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between">
    <span>(+) Penjualan Tunai:</span>
    <span>Rp {{ number_format($cashSales, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between">
    <span>(+) Top-Up / Cash-In:</span>
    <span>Rp {{ number_format($drawerCashIn, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between">
    <span>(-) Petty Cash / Cash-Out:</span>
    <span>Rp {{ number_format($drawerCashOut, 0, ',', '.') }}</span>
  </div>

  <div class="divider"></div>

  <div class="flex-between bold" style="font-size: 13px;">
    <span>ESTIMASI KAS DI LACI:</span>
    <span>Rp {{ number_format($expectedCash, 0, ',', '.') }}</span>
  </div>

  @if($drawerLogs->count() > 0)
    <div class="divider"></div>
    <div class="bold text-center" style="font-size: 11px;">MUTASI LACI TERAKHIR</div>
    <div class="divider"></div>
    @foreach($drawerLogs->take(5) as $log)
      <div class="flex-between" style="font-size: 11px;">
        <span>{{ $log->type == 'in' ? '[+]' : '[-]' }} {{ $log->created_at->format('H:i') }} {{ \Illuminate\Support\Str::limit($log->reason, 16) }}</span>
        <span>Rp {{ number_format($log->amount, 0, ',', '.') }}</span>
      </div>
    @endforeach
  @endif

  <div class="divider"></div>

  <div class="text-center" style="margin-top: 15px; font-size: 11px;">
    <div>*** PERHATIAN ***</div>
    <div>Ini adalah laporan audit sementara (X-Report).</div>
    <div>Bukan bukti penutupan kasir final (Z-Report).</div>
    <div>Sesi kasir MASIH AKTIF.</div>
    <div style="margin-top: 6px;">Dicetak oleh: {{ auth()->user()->name ?? 'Kasir' }}</div>
    <div style="margin-top: 8px;">================================</div>
  </div>

</body>
</html>
