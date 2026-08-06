@php
  $price = (float) $product->product_price;
  $activeDisc = $product->activeDiscount->first();
  $discountAmount = 0;
  if ($activeDisc) {
      $discType = $activeDisc->discount_type;
      $discValue = (float) ($activeDisc->discount_value ?? 0);
      if ($discType === 'percentage' && $discValue > 0) {
          $discountAmount = round($price * ($discValue / 100), 2);
      } elseif ($discType === 'nominal' && $discValue > 0) {
          $discountAmount = min($discValue, $price);
      }
  }
  $finalPrice = max(0, $price - $discountAmount);
@endphp

<div class="guest-product-card ms-glass-card rounded-xl overflow-hidden bento-card flex flex-col justify-between"
     data-id="{{ $product->product_id }}"
     data-name="{{ $product->product_name }}"
     data-price="{{ $finalPrice }}"
     data-cat="{{ $product->category_id }}">
  <div class="card-main-content">
    <!-- Product Image -->
    <div class="card-img-wrap relative h-48 w-full overflow-hidden bg-slate-900/60">
      @if($product->product_image)
        <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-108"/>
      @else
        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-800/40">
          <span class="material-symbols-outlined text-4xl mb-1">local_bar</span>
          <span class="text-xs font-semibold">No Image</span>
        </div>
      @endif
    </div>

    <!-- Product Info Body -->
    <div class="card-info-body p-4">
      <div class="flex items-center justify-between gap-1.5 mb-1">
        <span class="text-[10px] font-headline font-extrabold uppercase tracking-wider text-purple-300 opacity-90 truncate">
          {{ $product->category->category_name ?? 'Social Special' }}
        </span>
        <!-- Rating badge -->
        <div class="flex gap-0.5 items-center flex-shrink-0 bg-purple-950/40 px-1.5 py-0.5 rounded-full border border-purple-500/30">
          <span class="material-symbols-outlined text-purple-400 fill-icon text-[13px]">star</span>
          <span class="text-[10px] font-bold text-purple-300">4.9</span>
        </div>
      </div>

      <h3 class="font-headline font-bold text-slate-100 text-base mb-1.5 line-clamp-2 leading-tight">
        {{ $product->product_name }}
      </h3>

      <!-- Price & Discount Badge inside Card -->
      <div class="card-price-row flex items-baseline justify-between gap-1.5 mt-auto">
        <div class="flex items-baseline gap-1.5 flex-wrap">
          <span class="font-headline font-black text-purple-300 text-base">
            Rp {{ number_format($finalPrice, 0, ',', '.') }}
          </span>
          @if($discountAmount > 0)
            <span class="line-through text-slate-500 text-[11px] font-semibold">
              Rp {{ number_format($price, 0, ',', '.') }}
            </span>
          @endif
        </div>
        @if($discountAmount > 0)
          <span class="px-1.5 py-0.5 bg-purple-600 text-white text-[9px] font-headline font-black uppercase rounded shadow-xs">
            DISKON
          </span>
        @endif
      </div>
    </div>
  </div>

  <div class="card-action-wrap p-4 pt-0">
    <button type="button" 
            class="btn-add-cart mt-2 w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl flex items-center justify-center gap-1.5 transition-all font-headline font-bold text-xs active:scale-95 shadow-md"
            data-id="{{ $product->product_id }}">
      <span class="material-symbols-outlined text-[18px]">add</span>
      <span>TAMBAH</span>
    </button>
  </div>
</div>
