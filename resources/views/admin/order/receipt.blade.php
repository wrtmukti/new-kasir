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
        <th class="col-price">Disc</th>
        <th class="col-total">Sub</th>
      </tr>
    </thead>
    <tbody>
      @php $grandTotal = 0; @endphp
      @if($transaction && $transaction->items)
        @foreach($transaction->items as $item)
          @php
            $qty = (int) $item->qty;
            $price = (float) $item->price;
            $discount = (float) ($item->discount_amount ?? 0);
            $subtotal = (float) $item->subtotal;
            $grandTotal += $subtotal;
            $name = $item->product_name;
            if (strlen($name) > 16) $name = substr($name, 0, 14) . '..';
          @endphp
          <tr>
            <td class="col-item">{{ $name }}</td>
            <td class="col-qty">{{ $qty }}</td>
            <td class="col-price">{{ number_format($price, 0) }}</td>
            <td class="col-price">@if($discount > 0)-{{ number_format($discount, 0) }}@else-@endif</td>
            <td class="col-total">{{ number_format($subtotal, 0) }}</td>
          </tr>
          @if($item->note)
            <tr>
              <td colspan="5" style="font-size:10px;color:#666;padding-left:4px;">— {{ $item->note }}</td>
            </tr>
          @endif
        @endforeach
      @else
        @foreach($order->products as $product)
          @php
            $qty = (int) $product->pivot->quantity;
            $price = (float) $product->product_price;
            $subtotal = $price * $qty;
            $grandTotal += $subtotal;
            $name = $product->product_name;
            if (strlen($name) > 16) $name = substr($name, 0, 14) . '..';
          @endphp
          <tr>
            <td class="col-item">{{ $name }}</td>
            <td class="col-qty">{{ $qty }}</td>
            <td class="col-price">{{ number_format($price, 0) }}</td>
            <td class="col-price">-</td>
            <td class="col-total">{{ number_format($subtotal, 0) }}</td>
          </tr>
          @if($product->pivot->note)
            <tr>
              <td colspan="5" style="font-size:10px;color:#666;padding-left:4px;">— {{ $product->pivot->note }}</td>
            </tr>
          @endif
        @endforeach
      @endif
      @foreach($order->bundles as $ob)
        @php
          $bSub = (float) $ob->subtotal;
          $grandTotal += $bSub;
          $bName = $ob->bundle_name;
          if (strlen($bName) > 16) $bName = substr($bName, 0, 14) . '..';
        @endphp
        <tr>
          <td class="col-item">{{ $bName }} (Paket)</td>
          <td class="col-qty">{{ $ob->quantity }}</td>
          <td class="col-price">{{ number_format($ob->bundle_price, 0) }}</td>
          <td class="col-price">-</td>
          <td class="col-total">{{ number_format($bSub, 0) }}</td>
        </tr>
        @if($ob->bundle)
          @foreach($ob->bundle->items as $bi)
            <tr>
              <td colspan="5" style="font-size:10px;color:#666;padding-left:6px;">├ {{ $bi->product?->product_name ?? 'Produk' }} x{{ $bi->quantity }}</td>
            </tr>
          @endforeach
        @endif
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4">Subtotal</td>
        <td class="col-total">{{ number_format($grandTotal, 0) }}</td>
      </tr>
      @if($order->vouchers->isNotEmpty())
        @foreach($order->vouchers as $v)
        <tr>
          <td colspan="4" style="font-size:10px;">Voucher ({{ $v->voucher_code }})</td>
          <td class="col-total" style="font-size:10px;">-{{ number_format($v->voucher_amount, 0) }}</td>
        </tr>
        @endforeach
      @endif
      @if($order->service_charge_amount > 0)
        <tr>
          <td colspan="4" style="font-size:10px;">Service Charge ({{ number_format($order->service_charge_percent, 0) }}%)</td>
          <td class="col-total" style="font-size:10px;">{{ number_format($order->service_charge_amount, 0) }}</td>
        </tr>
      @endif
      @if($order->tax_amount > 0)
        <tr>
          <td colspan="4" style="font-size:10px;">PB1 Restoran ({{ number_format($order->tax_percent, 0) }}%)</td>
          <td class="col-total" style="font-size:10px;">{{ number_format($order->tax_amount, 0) }}</td>
        </tr>
      @endif
      <tr style="border-top: 1px solid #000; font-size: 13px;">
        <td colspan="4" style="padding-top:4px;">TOTAL</td>
        <td class="col-total" style="padding-top:4px;">{{ number_format($order->order_grand_total ?? $grandTotal, 0) }}</td>
      </tr>
      @if($transaction && $transaction->payment)
        @php
          $pm = $transaction->payment;
          $change = max(0, (float)$pm->payment_amount - (float)$pm->payment_grand_total);
        @endphp
        <tr style="font-size:11px;">
          <td colspan="4" style="padding-top:4px;">BAYAR ({{ strtoupper($pm->payment_metode) }})</td>
          <td class="col-total" style="padding-top:4px;">{{ number_format($pm->payment_amount, 0) }}</td>
        </tr>
        @if($pm->payment_metode === 'cash')
          <tr style="font-size:11px;">
            <td colspan="4">KEMBALI</td>
            <td class="col-total">{{ number_format($change, 0) }}</td>
          </tr>
        @elseif($pm->payment_reference)
          <tr style="font-size:10px; color:#555;">
            <td colspan="5" style="text-align:left; padding-top:2px;">Ref/Trace: {{ $pm->payment_reference }}</td>
          </tr>
        @endif
      @endif
    </tfoot>
  </table>

  <hr class="receipt-divider">

  <div class="receipt-footer">
    <p>Terima kasih atas kunjungan Anda!</p>
    <p style="margin-top:4px;">— Struk Resmi Pembayaran Kasir —</p>
  </div>

</div>

<script>
window.onafterprint = function() { window.close(); };
</script>
</body>
</html>
