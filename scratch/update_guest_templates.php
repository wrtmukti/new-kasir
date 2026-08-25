<?php

$themes = ['metropolis_brew', 'ignite_spice', 'midnight_social', 'omah_kopi_jogja', 'bumblebee'];
$baseDir = __DIR__ . '/../resources/views/guest';

foreach ($themes as $theme) {
    // 1. Review
    $reviewFile = "$baseDir/$theme/review.blade.php";
    if (file_exists($reviewFile)) {
        $content = file_get_contents($reviewFile);
        $oldNotice = '<p class="text-xs text-center text-on-surface-variant mt-4 flex items-center justify-center gap-1">
    <span class="material-symbols-outlined text-[16px] text-tertiary">info</span>
    Pesanan akan langsung diteruskan ke dapur cafe.
  </p>';
        $oldNotice2 = '<p class="text-xs text-center text-on-surface-variant mt-4 flex items-center justify-center gap-1">
    <span class="material-symbols-outlined text-[16px] text-tertiary">info</span>
    Pesanan akan langsung diteruskan ke dapur.
  </p>';
        $newNotice = '@if(($paymentTiming ?? \'post_payment\') === \'pre_payment\')
    <div class="mt-4 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-start gap-2.5 shadow-xs">
      <span class="material-symbols-outlined text-amber-600 flex-shrink-0 text-[18px]">info</span>
      <div>
        <strong class="font-bold">Mode Bayar di Awal:</strong>
        <p class="mt-0.5 text-amber-800">Setelah mengirim pesanan, silakan tunjukkan nomor meja ke kasir untuk melakukan pembayaran agar pesanan segera dimasak dapur.</p>
      </div>
    </div>
  @else
    <p class="text-xs text-center text-on-surface-variant mt-4 flex items-center justify-center gap-1">
      <span class="material-symbols-outlined text-[16px] text-tertiary">info</span>
      Pesanan akan langsung diteruskan ke dapur cafe.
    </p>
  @endif';
        
        if (strpos($content, '$paymentTiming') === false) {
            if (strpos($content, $oldNotice) !== false) {
                $content = str_replace($oldNotice, $newNotice, $content);
                file_put_contents($reviewFile, $content);
                echo "Updated review for $theme\n";
            } elseif (strpos($content, $oldNotice2) !== false) {
                $content = str_replace($oldNotice2, $newNotice, $content);
                file_put_contents($reviewFile, $content);
                echo "Updated review (v2) for $theme\n";
            } else {
                echo "Notice pattern not matched in review for $theme\n";
            }
        }
    }

    // 2. Status
    $statusFile = "$baseDir/$theme/status.blade.php";
    if (file_exists($statusFile)) {
        $content = file_get_contents($statusFile);
        if (strpos($content, 'isPaid') === false) {
            // Replace php status block
            $targetPhp = '@php
          $status = $order->order_status;
          $statusLabel = match($status) {
            \'pending\' => \'Menunggu Konfirmasi\',
            \'in_progress\' => \'Sedang Dimasak\',
            \'completed\' => \'Selesai\',
            \'cancelled\' => \'Dibatalkan\',
            default => ucfirst($status),
          };
          $stepActive = $status === \'in_progress\' || $status === \'completed\';
          $stepDone = $status === \'completed\';
        @endphp';

            $replPhp = '@php
          $status = $order->order_status;
          $isPaid = $order->isPaid();
          $isPre = ($paymentTiming ?? \'post_payment\') === \'pre_payment\';
          
          if ($isPre) {
            $statusLabel = match($status) {
              \'pending\' => \'Menunggu Pembayaran Kasir\',
              \'in_progress\' => ($isPaid ? \'Lunas & Sedang Dimasak\' : \'Sedang Dimasak\'),
              \'completed\' => \'Selesai Disajikan\',
              \'cancelled\' => \'Dibatalkan\',
              default => ucfirst($status),
            };
          } else {
            $statusLabel = match($status) {
              \'pending\' => \'Menunggu Konfirmasi\',
              \'in_progress\' => \'Sedang Dimasak\',
              \'completed\' => \'Selesai & Lunas\',
              \'cancelled\' => \'Dibatalkan\',
              default => ucfirst($status),
            };
          }
          $stepActive = $status === \'in_progress\' || $status === \'completed\';
          $stepDone = $status === \'completed\';
        @endphp';

            if (strpos($content, $targetPhp) !== false) {
                $content = str_replace($targetPhp, $replPhp, $content);
            }

            // Replace header badge
            $oldBadge = '<span class="px-3.5 py-1 rounded-full font-headline font-extrabold text-xs flex items-center gap-1.5 shadow-xs';
            $newBadge = '<div class="flex items-center gap-1.5 flex-wrap">
              @if($isPaid)
                <span class="px-2.5 py-0.5 rounded-full font-headline font-bold text-[11px] bg-green-100 text-green-800 flex items-center gap-1">
                  <span class="material-symbols-outlined text-[14px]">check</span> Lunas
                </span>
              @else
                <span class="px-2.5 py-0.5 rounded-full font-headline font-bold text-[11px] bg-amber-100 text-amber-800 flex items-center gap-1">
                  <span class="material-symbols-outlined text-[14px]">schedule</span> Belum Bayar
                </span>
              @endif

              <span class="px-3.5 py-1 rounded-full font-headline font-extrabold text-xs flex items-center gap-1.5 shadow-xs';

            // Replace stepper step 1
            $oldStep1 = '<span class="font-headline font-bold text-xs text-on-background">Diterima</span>';
            $newStep1 = '<span class="font-headline font-bold text-xs text-on-background">{{ $isPre ? ($isPaid ? \'Sudah Bayar\' : \'Bayar Kasir\') : \'Diterima\' }}</span>';

            // Insert alert before Stepper
            $searchBeforeStepper = '<!-- Tracking Stepper Bar -->';
            $alertBox = '@if($isPre && !$isPaid && $status === \'pending\')
            <div class="mb-4 p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-xs flex items-start gap-2.5 shadow-xs">
              <span class="material-symbols-outlined text-amber-600 flex-shrink-0 text-[20px]">payments</span>
              <div>
                <strong class="font-bold">Silakan Lakukan Pembayaran di Kasir:</strong>
                <p class="mt-0.5 text-amber-800">Tunjukkan nomor pesanan <strong>#{{ $order->order_id }}</strong> atau Meja {{ $table->table_number }} ke kasir dengan total <strong>Rp {{ number_format($order->order_grand_total, 0, \',\', \'.\') }}</strong> agar pesanan segera dimasak.</p>
              </div>
            </div>
          @endif

          <!-- Tracking Stepper Bar -->';

            // Also replace closing tag for badge container
            $oldBadgeClose = '</span>
          </div>';
            $newBadgeClose = '</span>
            </div>
          </div>';

            if (strpos($content, $oldBadge) !== false) {
                $content = str_replace($oldBadge, $newBadge, $content);
                $content = str_replace($oldBadgeClose, $newBadgeClose, $content);
                $content = str_replace($searchBeforeStepper, $alertBox, $content);
                $content = str_replace($oldStep1, $newStep1, $content);
                file_put_contents($statusFile, $content);
                echo "Updated status for $theme\n";
            } else {
                echo "Badge pattern not matched in status for $theme\n";
            }
        }
    }
}
