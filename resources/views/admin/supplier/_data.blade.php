@forelse($suppliers as $supplier)
<tr>
  <td class="text-mono">{{ $supplier->supplier_code ?? '-' }}</td>
  <td class="cell-primary">
    <a href="{{ route('admin.supplier.show', $supplier) }}" class="text-decoration-none">{{ $supplier->supplier_name }}</a>
  </td>
  <td>{{ $supplier->supplier_contact ?? '-' }}</td>
  <td class="text-mono">{{ $supplier->supplier_phone ?? '-' }}</td>
  <td>
    @if($supplier->supplier_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.supplier.edit', $supplier) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.supplier.destroy', $supplier) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="6" class="text-center text-muted-c py-4">Belum ada data supplier.</td>
</tr>
@endforelse
