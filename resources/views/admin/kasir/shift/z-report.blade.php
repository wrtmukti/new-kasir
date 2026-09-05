<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Z-Report Shift Closing #{{ $dailyClosing->id }}</title>
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
    @media print {
      body { width: 100%; padding: 0; }
      @page { margin: 0; }
    }
  </style>
</head>
<body onload="window.print()">

  <div class="text-center">
    <div class="bold" style="font-size: 15px;">STRUK REKAP Z-REPORT</div>
    <div>SESSION SHIFT CLOSING</div>
    <div class="bold" style="font-size: 13px; margin-top: 4px;">GEPREK GAMBUS RESTO</div>
  </div>

  <div class="divider"></div>

  <div class="flex-between">
    <span>ID Sesi Shift:</span>
    <span class="bold">#{{ $dailyClosing->id }}</span>
  </div>
  <div class="flex-between">
    <span>Tanggal Bisnis:</span>
    <span>{{ \Carbon\Carbon::parse($dailyClosing->business_date)->format('d/m/Y') }}</span>
  </div>
  <div class="flex-between">
    <span>Nama Shift:</span>
    <span class="bold">{{ $dailyClosing->shift_name }}</span>
  </div>
  <div class="flex-between">
    <span>Kasir Bertugas:</span>
    <span>ID #{{ $dailyClosing->cashier_id }}</span>
  </div>

  <div class="divider"></div>

  <div class="flex-between">
    <span>Jam Clock-In:</span>
    <span>{{ \Carbon\Carbon::parse($dailyClosing->opened_at)->format('d/m/Y H:i') }}</span>
  </div>
  <div class="flex-between">
    <span>Jam Clock-Out:</span>
    <span>{{ $dailyClosing->closed_at ? \Carbon\Carbon::parse($dailyClosing->closed_at)->format('d/m/Y H:i') : 'SEDESANG BERJALAN' }}</span>
  </div>

  <div class="double-divider"></div>
  <div class="bold text-center">RINCIAN CASH BALANCING LACI</div>
  <div class="double-divider"></div>

  <div class="flex-between">
    <span>Modal Awal Kasir:</span>
    <span>Rp {{ number_format($dailyClosing->starting_cash, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between">
    <span>(+) Penjualan Tunai:</span>
    <span>Rp {{ number_format($dailyClosing->system_cash_sales, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between">
    <span>(+) Cash-In Tambahan:</span>
    <span>Rp {{ number_format($dailyClosing->cash_in_amount, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between">
    <span>(-) Cash-Out Biaya:</span>
    <span>Rp {{ number_format($dailyClosing->cash_out_amount, 0, ',', '.') }}</span>
  </div>

  <div class="divider"></div>

  <div class="flex-between bold">
    <span>Ekspektasi Kas Laci:</span>
    <span>Rp {{ number_format($dailyClosing->system_expected_cash, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between bold" style="font-size: 13px;">
    <span>Fisik Kasir Counted:</span>
    <span>Rp {{ number_format($dailyClosing->actual_cash_counted, 0, ',', '.') }}</span>
  </div>

  <div class="divider"></div>

  <div class="flex-between bold" style="font-size: 13px;">
    <span>SELISIH KAS (VARIANCE):</span>
    <span>
      @if($dailyClosing->cash_difference == 0)
        PAS (Rp 0)
      @elseif($dailyClosing->cash_difference > 0)
        OVER (+Rp {{ number_format($dailyClosing->cash_difference, 0, ',', '.') }})
      @else
        SHORT (-Rp {{ number_format(abs($dailyClosing->cash_difference), 0, ',', '.') }})
      @endif
    </span>
  </div>

  <div class="double-divider"></div>

  <div class="flex-between">
    <span>Total Sales Non-Tunai:</span>
    <span>Rp {{ number_format($dailyClosing->system_non_cash_sales, 0, ',', '.') }}</span>
  </div>
  <div class="flex-between bold">
    <span>TOTAL OMZET SHIFT:</span>
    <span>Rp {{ number_format($dailyClosing->system_cash_sales + $dailyClosing->system_non_cash_sales, 0, ',', '.') }}</span>
  </div>

  @if($dailyClosing->notes)
    <div class="divider"></div>
    <div>Catatan: {{ $dailyClosing->notes }}</div>
  @endif

  <div class="divider"></div>

  <div class="text-center" style="margin-top: 15px;">
    <div>*** SHIFT CLOSING VERIFIED ***</div>
    <div style="font-size: 10px; color: #555;">Dicetak pada {{ date('d/m/Y H:i:s') }}</div>
  </div>

</body>
</html>
