@php
  $bPrice = (float) $bundle->bundle_price;
  $bundleItemsData = $bundle->items->map(function($bi) {
      return [
          'product_id' => $bi->product_id,
          'quantity' => $bi->quantity,
          'product_name' => $bi->product->product_name ?? 'Produk'
      ];
  })->values()->toArray();
@endphp

<div class="guest-bundle-card bg-surface-container-low rounded-xl overflow-hidden shadow-xs hover:shadow-md border border-outline-variant bento-card flex flex-col justify-between"
     data-bundle-id="{{ $bundle->bundle_id }}"
     data-bundle-name="{{ $bundle->bundle_name }}"
     data-bundle-price="{{ $bPrice }}"
     data-bundle-items="{{ json_encode($bundleItemsData) }}">
  <div>
    <div class="relative h-44 w-full overflow-hidden bg-amber-400 flex items-center justify-center">
      <div class="w-16 h-16 rounded-2xl bg-white/30 text-amber-950 flex items-center justify-center shadow-lg backdrop-blur">
        <span class="material-symbols-outlined text-3xl fill-icon">emoji_nature</span>
      </div>
      <div class="absolute top-3 right-3 bg-slate-900 text-amber-300 font-headline font-black text-xs px-3 py-1 rounded-full shadow-md">
        PAKET HONEY
      </div>
    </div>

    <div class="p-4">
      <h3 class="font-headline font-extrabold text-on-background text-base truncate">{{ $bundle->bundle_name }}</h3>
      <div class="text-xs text-on-surface-variant mt-1.5 space-y-1">
        @foreach($bundle->items as $bi)
          <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px] text-amber-600">check_circle</span>
            <span>{{ $bi->product->product_name ?? 'Produk' }} (x{{ $bi->quantity }})</span>
          </div>
        @endforeach
      </div>
      <div class="mt-3 font-headline font-black text-amber-800 text-lg">
        Rp {{ number_format($bPrice, 0, ',', '.') }}
      </div>
    </div>
  </div>

  <div class="p-4 pt-0">
    <button type="button" 
            class="btn-add-bundle mt-2 w-full bg-amber-400 hover:bg-amber-500 text-slate-900 py-2.5 rounded-xl flex items-center justify-center gap-1.5 transition-all font-headline font-extrabold text-xs active:scale-95 shadow-xs"
            data-bundle-id="{{ $bundle->bundle_id }}">
      <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
      <span>TAMBAH PAKET</span>
    </button>
  </div>
</div>
