@if(isset($isStoreOpen) && !$isStoreOpen)
{{-- ==============================================================================
     GUEST STORE CLOSED ALERT BANNER & GLOBAL ORDERING GUARD
     Tampil di atas Hero Section ketika restoran berstatus tutup / shift kasir belum buka
     ============================================================================== --}}
<div class="store-closed-banner-wrap mb-6 w-full" id="storeClosedNotice">
  <div class="store-closed-card rounded-2xl p-4 md:p-5 border shadow-sm transition-all" 
       style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.09), rgba(239, 68, 68, 0.06)); border-color: rgba(245, 158, 11, 0.35);">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5">
      <div class="flex items-start gap-3 min-w-0">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 shadow-xs"
             style="background: rgba(245, 158, 11, 0.18); color: #d97706;">
          <span class="material-symbols-outlined text-[24px]">storefront</span>
        </div>
        <div>
          <div class="flex items-center gap-2 flex-wrap">
            <h4 class="font-bold text-sm sm:text-base leading-snug" style="color: #b45309;">
              Restoran Sedang Tutup
            </h4>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide"
                  style="background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.25);">
              Pemesanan Ditutup
            </span>
          </div>
          <p class="text-xs sm:text-sm mt-1 leading-relaxed text-slate-600 dark:text-slate-300">
            Dapur &amp; kasir kami saat ini belum membuka sesi pemesanan menu via QR. Anda tetap dapat melihat seluruh daftar menu, foto, dan harga kami di bawah ini.
          </p>
        </div>
      </div>
      
      @php
        $statusUrl = '#';
        try {
          if (isset($client, $outlet, $table)) {
            $statusUrl = route('guest.status', [$client->client_id, $outlet->outlet_id, $table->table_id]);
          } elseif (isset($table)) {
            $statusUrl = route('guest.status', $table->table_id);
          }
        } catch (\Throwable $e) {
          $statusUrl = '#';
        }
      @endphp
      <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto pt-1 sm:pt-0">
        <a href="{{ $statusUrl }}" 
           class="w-full sm:w-auto px-3.5 py-2 rounded-xl text-xs font-bold transition-all text-center flex items-center justify-center gap-1.5 shadow-xs"
           style="background: #ffffff; color: #b45309; border: 1px solid rgba(245, 158, 11, 0.4);">
          <span class="material-symbols-outlined text-[16px]">receipt_long</span>
          <span>Cek Pesanan Saya</span>
        </a>
      </div>
    </div>
  </div>
</div>

<style>
  /* Global store closed styling to neutralize all guest ordering actions */
  .store-closed-disabled,
  .btn-add-cart,
  .btn-add-bundle {
    opacity: 0.55 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
    filter: grayscale(35%) !important;
    background: #94a3b8 !important;
    border-color: #94a3b8 !important;
    color: #ffffff !important;
  }
  .sb-cart-bar, .ok-cart-bar, .mb-cart-bar, .ms-cart-bar, .is-cart-bar, .bb-cart-bar,
  #sbCartBar, #okCartBar, #mbCartBar, #msCartBar, #isCartBar, #bbCartBar,
  #sbCartSheet, #okCartSheet, #mbCartSheet, #msCartSheet, #isCartSheet, #bbCartSheet,
  #guestCartFloat, .guest-cart-float, #guestCartSheet,
  #sbCartBackdrop, #okCartBackdrop, #mbCartBackdrop, #msCartBackdrop, #isCartBackdrop, #bbCartBackdrop {
    display: none !important;
  }
</style>

<script>
  (function() {
    function disableGuestOrdering() {
      // 1. Disable all product and bundle add buttons
      document.querySelectorAll('.btn-add-cart, .btn-add-bundle').forEach(function(btn) {
        btn.setAttribute('disabled', 'disabled');
        btn.classList.add('store-closed-disabled');
        btn.innerHTML = '<span class="material-symbols-outlined text-[16px]" style="font-size:16px;">lock</span> <span style="font-size:11px; margin-left:4px;">TOKO TUTUP</span>';
      });

      // 2. Hide and disable cart bars and checkout buttons
      var cartBars = [
        'sbCartBar', 'okCartBar', 'mbCartBar', 'msCartBar', 'isCartBar', 'bbCartBar', 'guestCartFloat'
      ];
      cartBars.forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.style.setProperty('display', 'none', 'important');
      });

      // 3. Disable item modal submit buttons
      var addBtns = [
        'sbAddToCartBtn', 'okAddToCartBtn', 'mbAddToCartBtn', 'msAddToCartBtn', 'isAddToCartBtn', 'bbAddToCartBtn'
      ];
      addBtns.forEach(function(id) {
        var btn = document.getElementById(id);
        if (btn) {
          btn.setAttribute('disabled', 'disabled');
          btn.classList.add('store-closed-disabled');
          btn.innerHTML = '<span>Pemesanan Sedang Ditutup</span>';
        }
      });

      // 4. Block forms
      var forms = [
        'sbCheckoutForm', 'okCheckoutForm', 'mbCheckoutForm', 'msCheckoutForm', 'isCheckoutForm', 'bbCheckoutForm'
      ];
      forms.forEach(function(id) {
        var form = document.getElementById(id);
        if (form) {
          form.onsubmit = function(e) {
            e.preventDefault();
            alert('Mohon maaf, restoran saat ini sedang tutup dan belum dapat menerima pesanan.');
            return false;
          };
        }
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', disableGuestOrdering);
    } else {
      disableGuestOrdering();
    }
    setTimeout(disableGuestOrdering, 300);
  })();
</script>
@endif
