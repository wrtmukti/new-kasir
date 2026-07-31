<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    // ============ STOCK HISTORY ============

    public function stockShow($id)
    {
        $history = DB::table('stock_histories')->where('stock_history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.stock.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        // Cari record sebelumnya (buat perbandingan)
        $previous = DB::table('stock_histories')
            ->where('stock_id', $history->stock_id)
            ->where('stock_history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.history.stock.show', compact('history', 'previous'));
    }

    public function stockIndex()
    {
        $histories = DB::table('stock_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.history.stock.index', compact('histories'));
    }

    public function stockData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('stock_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('action_type', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.history.stock._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.history.stock.index', compact('histories'));
    }

    // ============ DISCOUNT HISTORY ============

    public function discountShow($id)
    {
        $history = DB::table('discount_histories')->where('discount_history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.discount.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        $previous = DB::table('discount_histories')
            ->where('discount_id', $history->discount_id)
            ->where('discount_history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.history.discount.show', compact('history', 'previous'));
    }

    public function discountIndex()
    {
        $histories = DB::table('discount_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.history.discount.index', compact('histories'));
    }

    public function discountData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('discount_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('reason', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.history.discount._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.history.discount.index', compact('histories'));
    }

    // ============ BUNDLE HISTORY ============

    public function bundleShow($id)
    {
        $history = DB::table('bundle_histories')->where('bundle_history_id', $id)->first();

        if (!$history) {
            return redirect()->route('admin.history.bundle.index')
                ->with('error', 'Riwayat tidak ditemukan.');
        }

        $previous = DB::table('bundle_histories')
            ->where('bundle_id', $history->bundle_id)
            ->where('bundle_history_id', '<', $id)
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        return view('admin.history.bundle.show', compact('history', 'previous'));
    }

    public function bundleIndex()
    {
        $histories = DB::table('bundle_histories')
            ->where('delete_status', 0)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.history.bundle.index', compact('histories'));
    }

    public function bundleData(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $actionFilter = $request->input('action_type');

        $query = DB::table('bundle_histories')->where('delete_status', 0);

        if ($actionFilter && $actionFilter !== '') {
            $query->where('action_type', $actionFilter);
        }

        $histories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.history.bundle._data', compact('histories'))->render(),
                'pagination' => $histories->links('vendor.pagination.modern')->toHtml(),
                'total' => $histories->total(),
                'from' => $histories->firstItem(),
                'to' => $histories->lastItem(),
            ]);
        }

        return view('admin.history.bundle.index', compact('histories'));
    }
}
