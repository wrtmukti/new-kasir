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

<div class="guest-bundle-card ms-glass-card rounded-xl overflow-hidden bento-card flex flex-col justify-between"
     data-bundle-id="{{ $bundle->bundle_id }}"
     data-bundle-name="{{ $bundle->bundle_name }}"
     data-bundle-price="{{ $bPrice }}"
     data-bundle-items="{{ json_encode($bundleItemsData) }}">
  <div>
    <div class="relative h-44 w-full overflow-hidden bg-purple-950/40 flex items-center justify-center border-b border-white/5">
      <div class="w-16 h-16 rounded-2xl bg-purple-600 text-white flex items-center justify-center shadow-lg">
        <span class="material-symbols-outlined text-3xl fill-icon">nightlife</span>
      </div>
      <div class="absolute top-3 right-3 bg-purple-600 text-white font-headline font-extrabold text-xs px-3 py-1 rounded-full shadow-md">
        PAKET MIDNIGHT
      </div>
    </div>

    <div class="p-4">
      <h3 class="font-headline font-extrabold text-slate-100 text-base truncate">{{ $bundle->bundle_name }}</h3>
      <div class="text-xs text-slate-400 mt-1.5 space-y-1">
        @foreach($bundle->items as $bi)
          <div class="flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[14px] text-purple-400">check_circle</span>
            <span>{{ $bi->product->product_name ?? 'Produk' }} (x{{ $bi->quantity }})</span>
          </div>
        @endforeach
      </div>
      <div class="mt-3 font-headline font-black text-purple-300 text-lg">
        Rp {{ number_format($bPrice, 0, ',', '.') }}
      </div>
    </div>
  </div>

  <div class="p-4 pt-0">
    <button type="button" 
            class="btn-add-bundle mt-2 w-full bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl flex items-center justify-center gap-1.5 transition-all font-headline font-extrabold text-xs active:scale-95 shadow-md"
            data-bundle-id="{{ $bundle->bundle_id }}">
      <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
      <span>TAMBAH PAKET</span>
    </button>
  </div>
</div>
