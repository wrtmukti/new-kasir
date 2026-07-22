<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk #{{ $order->order_id }}</title>
<style>
@page { margin: 0; size: 80mm auto; }
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Courier New', monospace;
  font-size: 12px;
  color: #000;
  width: 80mm;
  padding: 8mm 5mm;
}
.receipt {
  text-align: center;
}
.receipt-header {
  margin-bottom: 6px;
}
.receipt-header .title {
  font-size: 16px;
  font-weight: bold;
  letter-spacing: 1px;
  text-transform: uppercase;
}
.receipt-header .info {
  font-size: 11px;
  margin-top: 2px;
}
.receipt-divider {
  border: none;
  border-top: 1px dashed #000;
  margin: 6px 0;
}
.receipt-meta {
  text-align: left;
  margin-bottom: 4px;
  font-size: 11px;
}
.receipt-meta div { margin-bottom: 1px; }
.receipt-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 11px;
}
.receipt-table th {
  text-align: left;
  border-bottom: 1px dashed #000;
  padding: 3px 0;
}
.receipt-table td {
  padding: 2px 0;
  vertical-align: top;
}
.receipt-table .col-item { text-align: left; }
.receipt-table .col-qty { text-align: center; width: 8mm; }
.receipt-table .col-price { text-align: right; width: 22mm; }
.receipt-table .col-total { text-align: right; width: 22mm; }
.receipt-table tfoot td {
  border-top: 1px dashed #000;
  padding-top: 4px;
  font-weight: bold;
}
.receipt-footer {
  margin-top: 8px;
  text-align: center;
  font-size: 11px;
}
.receipt-footer p { margin-bottom: 2px; }
</style>
</head>
<body onload="window.print()">

<div class="receipt">

  <div class="receipt-header">
    <div class="title">{{ $company->company_name ?? 'Kasir POS' }}</div>
    @if($company)
      <div class="info">{{ $company->company_address ?? '' }}</div>
      <div class="info">{{ $company->company_phone ?? '' }}</div>
    @endif
  </div>

  <hr class="receipt-divider">

  <div class="receipt-meta">
    <div><strong>No.</strong> #{{ $order->order_id }}</div>
    <div><strong>Tgl.</strong> {{ $order->created_at->format('d/m/Y H:i') }}</div>
    <div><strong>Tipe</strong> {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</div>
    @if($table)
      <div><strong>Meja</strong> {{ $table->table_number }}</div>
    @endif
  </div>

  <hr class="receipt-divider">

  <table class="receipt-table">
    <thead>
      <tr>
        <th class="col-item">Item</th>
        <th class="col-qty">Qty</th>
        <th class="col-price">Harga</th>
        <th class="col-total">Sub</th>
      </tr>
    </thead>
    <tbody>
      @php $grandTotal = 0; @endphp
      @foreach($order->products as $product)
        @php
          $qty = (int) $product->pivot->quantity;
          $price = (float) $product->product_price;
          $subtotal = $price * $qty;
          $grandTotal += $subtotal;
          $name = $product->product_name;
          if (strlen($name) > 20) $name = substr($name, 0, 18) . '..';
        @endphp
        <tr>
          <td class="col-item">{{ $name }}</td>
          <td class="col-qty">{{ $qty }}</td>
          <td class="col-price">{{ number_format($price, 0) }}</td>
          <td class="col-total">{{ number_format($subtotal, 0) }}</td>
        </tr>
        @if($product->pivot->note)
          <tr>
            <td colspan="4" style="font-size:10px;color:#666;padding-left:4px;">— {{ $product->pivot->note }}</td>
          </tr>
        @endif
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">Total</td>
        <td class="col-total">{{ number_format($grandTotal, 0) }}</td>
      </tr>
    </tfoot>
  </table>

  <hr class="receipt-divider">

  <div class="receipt-footer">
    <p>Terima kasih atas kunjungan Anda!</p>
    <p style="margin-top:6px;">— Barang yang sudah dibeli tidak dapat dikembalikan —</p>
  </div>

</div>

<script>
window.onafterprint = function() { window.close(); };
</script>
</body>
</html>
