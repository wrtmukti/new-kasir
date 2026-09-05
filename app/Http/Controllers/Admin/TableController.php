<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TableRequest;
use App\Models\Admin\Table;
use App\Models\Admin\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableController extends Controller
{
    protected function getActiveClientId(): string
    {
        $clientId = session('client_id') ?? session('tenant_client_id');
        if (!$clientId && session('client_database')) {
            $client = \App\Models\SysAdmin\Client::where('database_name', session('client_database'))->first();
            $clientId = $client?->client_id ?? '';
        }
        return (string) $clientId;
    }

    protected function getActiveOutletId(): ?string
    {
        return session('active_outlet_id') ?? session('outlet_id') ?? Outlet::where('delete_status', 0)->value('outlet_id');
    }

    public function index()
    {
        $clientId = $this->getActiveClientId();
        $activeOutletId = $this->getActiveOutletId();
        $query = Table::where('delete_status', 0)->with('outlet');
        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }
        $tables = $query->latest()->paginate(10);
        return view('admin.kasir.table.index', compact('tables', 'clientId'));
    }

    public function data(Request $request)
    {
        $clientId = $this->getActiveClientId();
        $activeOutletId = $this->getActiveOutletId();
        $perPage = $request->input('per_page', 10);
        $query = Table::where('delete_status', 0)->with('outlet');
        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }
        $tables = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.table._data', compact('tables', 'clientId'))->render(),
                'pagination' => $tables->links('vendor.pagination.modern')->toHtml(),
                'total' => $tables->total(),
                'from' => $tables->firstItem(),
                'to' => $tables->lastItem(),
            ]);
        }

        return view('admin.kasir.table.index', compact('tables', 'clientId'));
    }

    public function create()
    {
        $outlets = Outlet::where('delete_status', 0)->where('outlet_status', 1)->get();
        return view('admin.kasir.table.create', compact('outlets'));
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
        $table->load('outlet');
        return view('admin.kasir.table.show', compact('table'));
    }

    public function edit(Table $table)
    {
        $outlets = Outlet::where('delete_status', 0)->where('outlet_status', 1)->get();
        return view('admin.kasir.table.edit', compact('table', 'outlets'));
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
