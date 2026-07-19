<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TableRequest;
use App\Models\Admin\Table;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('admin.table.index', compact('tables'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $tables = Table::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.table._data', compact('tables'))->render(),
                'pagination' => $tables->links('vendor.pagination.modern')->toHtml(),
                'total' => $tables->total(),
                'from' => $tables->firstItem(),
                'to' => $tables->lastItem(),
            ]);
        }

        return view('admin.table.index', compact('tables'));
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.table.create', compact('companies'));
    }

    public function store(TableRequest $request)
    {
        $validated = $request->validated();

        $validated['table_id'] = (string) Str::ulid();
        $validated['table_status'] = $validated['table_status'] ?? 'active';

        Table::create($validated);

        return redirect()->route('admin.table.index')
            ->with('success', 'Meja berhasil ditambahkan.');
    }

    public function show(Table $table)
    {
        $table->load('company');
        return view('admin.table.show', compact('table'));
    }

    public function edit(Table $table)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.table.edit', compact('table', 'companies'));
    }

    public function update(TableRequest $request, Table $table)
    {
        $validated = $request->validated();
        $table->update($validated);

        return redirect()->route('admin.table.index')
            ->with('success', 'Meja berhasil diperbarui.');
    }

    public function destroy(Table $table)
    {
        $table->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Meja berhasil dihapus.']);
        }

        return redirect()->route('admin.table.index')
            ->with('success', 'Meja berhasil dihapus.');
    }
}
