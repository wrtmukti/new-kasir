@forelse($tables as $table)
<tr>
  <td class="cell-primary">
    <a href="{{ route('admin.table.show', $table) }}" class="text-decoration-none fw-semibold">Meja {{ $table->table_number }}</a>
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
  <td colspan="4" class="text-center text-muted-c py-4">Belum ada data meja.</td>
</tr>
@endforelse
