@forelse($categories as $category)
<tr class="row-clickable" data-url="{{ route('admin.category.edit', $category) }}">
  <td class="cell-primary">
    <div class="d-flex align-items-center gap-2">
      @if($category->category_image)
        <img src="{{ asset('storage/' . $category->category_image) }}" alt=""
             style="width:32px;height:32px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0;">
      @else
        <span style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:var(--radius-sm);background:var(--bg-elevated-2);flex-shrink:0;">
          <i class="bi bi-image" style="font-size:0.85rem;color:var(--text-muted);"></i>
        </span>
      @endif
      <span>{{ $category->category_name }}</span>
    </div>
  </td>
  <td>{{ $category->category_type ?? '-' }}</td>
  <td>
    @if($category->category_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('admin.category.edit', $category) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('admin.category.destroy', $category) }}" title="Hapus">
        <i class="bi bi-trash"></i>
      </button>
    </div>
  </td>
</tr>
@empty
<tr>
  <td colspan="4" class="text-center text-muted-c py-4">Belum ada data kategori.</td>
</tr>
@endforelse
