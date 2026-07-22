<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('admin.transaction.index', compact('transactions'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $transactions = Transaction::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.transaction._data', compact('transactions'))->render(),
                'pagination' => $transactions->links('vendor.pagination.modern')->toHtml(),
                'total' => $transactions->total(),
                'from' => $transactions->firstItem(),
                'to' => $transactions->lastItem(),
            ]);
        }

        return view('admin.transaction.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        if ($transaction->delete_status) {
            return redirect()->route('admin.transaction.index')
                ->with('error', 'Transaksi tidak ditemukan.');
        }

        $transaction->load('items');
        return view('admin.transaction.show', compact('transaction'));
    }
}
