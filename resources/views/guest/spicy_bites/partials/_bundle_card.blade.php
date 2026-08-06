@php
  $bPrice = (float) $bundle->bundle_price;
@endphp

<div class="guest-bundle-card bg-secondary-container/20 rounded-xl overflow-hidden shadow-sm border border-secondary-container bento-card flex flex-col justify-between"
     data-bundle-id="{{ $bundle->bundle_id }}"
     data-bundle-name="{{ $bundle->bundle_name }}"
     data-bundle-price="{{ $bPrice }}">
  <div>
    <div class="relative h-44 w-full overflow-hidden bg-secondary-container/30 flex items-center justify-center">
      <div class="w-16 h-16 rounded-full bg-secondary-container flex items-center justify-center shadow-md">
        <span class="material-symbols-outlined text-on-secondary-container text-3xl fill-icon pulse-flame">local_offer</span>
      </div>
      <div class="absolute top-3 right-3 bg-secondary-container text-on-secondary-container font-headline font-extrabold text-xs px-3 py-1 rounded-full shadow-sm">
        PAKET HEMAT
      </div>
    </div>

    <div class="p-4">
      <h3 class="font-headline font-extrabold text-on-background text-base truncate">{{ $bundle->bundle_name }}</h3>
      <div class="text-xs text-on-surface-variant mt-1 space-y-0.5">
        @foreach($bundle->items as $bi)
          <div class="flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px] text-primary">check_circle</span>
            <span>{{ $bi->product->product_name ?? 'Produk' }} (x{{ $bi->quantity }})</span>
          </div>
        @endforeach
      </div>
      <div class="mt-3 font-headline font-black text-primary text-lg">
        Rp {{ number_format($bPrice, 0, ',', '.') }}
      </div>
    </div>
  </div>

  <div class="p-4 pt-0">
    <button type="button" 
            class="btn-add-bundle mt-2 w-full bg-secondary-container hover:bg-secondary-fixed text-on-secondary-container py-2.5 rounded-lg flex items-center justify-center gap-1.5 transition-all font-headline font-extrabold text-xs active:scale-95 shadow-sm"
            data-bundle-id="{{ $bundle->bundle_id }}">
      <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
      TAMBAH PAKET
    </button>
  </div>
</div>
