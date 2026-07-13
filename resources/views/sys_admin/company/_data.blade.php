@forelse($companies as $company)
<tr>
  <td class="text-mono">{{ $company->company_code ?? '-' }}</td>
  <td class="cell-primary">
    <a href="{{ route('sys_admin.company.show', $company) }}" class="text-decoration-none">{{ $company->company_name }}</a>
  </td>
  <td>{{ $company->company_branch ?? '-' }}</td>
  <td>{{ $company->company_email ?? '-' }}</td>
  <td class="text-mono">{{ $company->company_phone ?? '-' }}</td>
  <td>
    @if($company->company_status)
      <span class="pill pill-success">Aktif</span>
    @else
      <span class="pill pill-neutral">Nonaktif</span>
    @endif
  </td>
  <td>
    <div class="d-flex gap-1">
      <a href="{{ route('sys_admin.company.edit', $company) }}" class="btn btn-ghost btn-icon-sq btn-sm" title="Edit">
        <i class="bi bi-pencil"></i>
      </a>
      <button type="button" class="btn btn-ghost btn-icon-sq btn-sm text-danger btn-delete" data-url="{{ route('sys_admin.company.destroy', $company) }}" title="Hapus">
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
