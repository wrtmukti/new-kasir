<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected function getActiveOutletId(): ?string
    {
        return session('active_outlet_id') ?? session('outlet_id') ?? Outlet::where('delete_status', 0)->value('outlet_id');
    }

    public function index()
    {
        $activeOutletId = $this->getActiveOutletId();
        $query = Transaction::where('delete_status', 0)->with('outlet');
        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }
        $transactions = $query->orderBy('transaction_id', 'desc')->paginate(10);
        return view('admin.kasir.transaction.index', compact('transactions'));
    }

    public function data(Request $request)
    {
        $activeOutletId = $this->getActiveOutletId();
        $perPage = $request->input('per_page', 10);
        $query = Transaction::where('delete_status', 0)->with('outlet');
        if ($activeOutletId) {
            $query->where('outlet_id', $activeOutletId);
        }
        $transactions = $query->orderBy('transaction_id', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.kasir.transaction._data', compact('transactions'))->render(),
                'pagination' => $transactions->links('vendor.pagination.modern')->toHtml(),
                'total' => $transactions->total(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem(),
            ]);
        }

        return view('admin.kasir.transaction.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->delete_status) {
            return redirect()->route('admin.transaction.index')
                ->with('error', 'Transaksi tidak ditemukan.');
        }

        $transaction->load('items', 'bundles.bundle.items.product');
        return view('admin.kasir.transaction.show', compact('transaction'));
    }
}
