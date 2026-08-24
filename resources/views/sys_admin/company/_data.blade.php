@forelse($outlets as $outlet)
<tr>
  <td class="text-mono">{{ $outlet->outlet_code ?? '-' }}</td>
  <td class="cell-primary">
    <a href="{{ route('sys_admin.company.show', $outlet) }}" class="text-decoration-none">{{ $outlet->outlet_name }}</a>
  </td>
  <td>{{ $outlet->outlet_branch ?? '-' }}</td>
  <td>{{ $outlet->outlet_email ?? '-' }}</td>
  <td class="text-mono">{{ $outlet->outlet_phone ?? '-' }}</td>
  <td>
    @if($outlet->outlet_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('sys_admin.company.edit', $outlet) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('sys_admin.company.destroy', $outlet) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="7" class="text-center text-muted-c py-4">Belum ada data perusahaan.</td>
</tr>
@endforelse
