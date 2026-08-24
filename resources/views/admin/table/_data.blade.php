@forelse($tables as $table)
@php
  $activeClient = $clientId ?? session('client_id') ?? session('tenant_client_id') ?? '';
  $activeOutlet = $table->outlet_id ?? $table->outlet?->outlet_id ?? \App\Models\Admin\Outlet::where('delete_status', 0)->value('outlet_id') ?? 'default';
  $guestUrl = url("{$activeClient}/{$activeOutlet}/{$table->table_id}");
@endphp
<tr>
  <td class="cell-primary">
    <a href="{{ route('admin.table.show', $table) }}" class="text-decoration-none fw-semibold">
      <i class="bi bi-tablet-landscape me-1.5 text-primary"></i>Meja {{ $table->table_number }}
    </a>
  </td>
  <td>
    <span class="badge bg-secondary-subtle text-secondary fw-semibold" style="font-size:0.75rem;">
      <i class="bi bi-shop me-1"></i>{{ $table->outlet->outlet_name ?? 'Outlet Utama' }}
    </span>
  </td>
  <td class="text-mono">{{ $table->table_capacity ?? '-' }} orang</td>
  <td>
    @switch($table->table_status ?? 'inactive')
      @case('active')
        <span class="pill pill-success">Kosong</span>
        @break
      @case('reserved')
        <span class="pill pill-warning">Dipesan</span>
        @break
      @case('occupied')
        <span class="pill pill-danger">Terisi</span>
        @break
      @default
        <span class="pill pill-neutral">Nonaktif</span>
    @endswitch
  </td>
  <td>
    <div class="d-flex align-items-center gap-1.5">
      <button type="button" class="btn btn-outline-soft btn-sm btn-copy-url py-1 px-2.5 d-inline-flex align-items-center gap-1" data-url="{{ $guestUrl }}" title="Salin Link Menu Meja (QR)">
        <i class="bi bi-clipboard"></i>
        <span>Salin URL</span>
      </button>
      <a href="{{ $guestUrl }}" target="_blank" class="btn btn-ghost btn-icon-sq btn-sm" title="Buka Halaman Menu Meja (Guest)">
        <i class="bi bi-box-arrow-up-right"></i>
      </a>
    </div>
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.table.edit', $table) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.table.destroy', $table) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted-c py-4">Belum ada data meja.</td>
</tr>
@endforelse
