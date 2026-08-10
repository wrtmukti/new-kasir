<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocsController;

// ===================== DOCS (Admin Template Nexora) =====================
Route::prefix('docs')->group(function () {

    // Overview
    Route::get('/index', [DocsController::class, 'index'])->name('docs.dashboard');

    // Analytics
    Route::get('/analytics', [DocsController::class, 'analytics'])->name('docs.analytics');

    // UI Components
    Route::get('/ui-components', [DocsController::class, 'uiComponents'])->name('docs.ui-components');
    Route::get('/components', [DocsController::class, 'components'])->name('docs.components');
    Route::get('/buttons', [DocsController::class, 'buttons'])->name('docs.buttons');
    Route::get('/cards', [DocsController::class, 'cards'])->name('docs.cards');
    Route::get('/forms', [DocsController::class, 'forms'])->name('docs.forms');
    Route::get('/tables', [DocsController::class, 'tables'])->name('docs.tables');
    Route::get('/charts', [DocsController::class, 'charts'])->name('docs.charts');
    Route::get('/icons', [DocsController::class, 'icons'])->name('docs.icons');

    // Management
    Route::get('/users', [DocsController::class, 'users'])->name('docs.users');
    Route::get('/settings', [DocsController::class, 'settings'])->name('docs.settings');
    Route::get('/profile', [DocsController::class, 'profile'])->name('docs.profile');
    Route::get('/pricing', [DocsController::class, 'pricing'])->name('docs.pricing');
    Route::get('/kanban', [DocsController::class, 'kanban'])->name('docs.kanban');
    Route::get('/invoice', [DocsController::class, 'invoice'])->name('docs.invoice');
    Route::get('/blank', [DocsController::class, 'blank'])->name('docs.blank');

    // Resources
    Route::get('/documentation', [DocsController::class, 'documentation'])->name('docs.documentation');
    Route::get('/auth-login', [DocsController::class, 'authLogin'])->name('docs.auth-login');

    // Error
    Route::get('/404', [DocsController::class, 'error404'])->name('docs.404');
});

// Welcome route
Route::get('/', function () {
    return view('welcome');
});

// Redirect bare /docs → /docs/index
Route::get('/docs', function () {
    return redirect('/docs/index');
})->name('docs.index');

// ===================== ADMIN (CRUD) =====================
use App\Http\Controllers\SysAdmin\CompanyController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\BundleController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\Keuangan\CogsRawMaterialController;
use App\Http\Controllers\Admin\Keuangan\CogsRecipeController;
use App\Http\Controllers\Admin\Keuangan\CogsWasteLogController;
use App\Http\Controllers\Admin\Keuangan\HppReportController;
use App\Http\Controllers\Admin\Keuangan\MenuAnalyticsController;
use App\Http\Controllers\Admin\Keuangan\PurchaseOrderController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('stock/data', [StockController::class, 'data'])->name('stock.data');
    Route::get('stock/json-list', [StockController::class, 'jsonList'])->name('stock.json-list');
    Route::resource('stock', StockController::class);

    Route::get('product/data', [ProductController::class, 'data'])->name('product.data');
    Route::resource('product', ProductController::class);

    Route::get('category/data', [CategoryController::class, 'data'])->name('category.data');
    Route::resource('category', CategoryController::class);

    Route::get('table/data', [TableController::class, 'data'])->name('table.data');
    Route::resource('table', TableController::class);

    Route::get('supplier/data', [SupplierController::class, 'data'])->name('supplier.data');
    Route::resource('supplier', SupplierController::class);

    // Bundle
    Route::get('bundle/data', [BundleController::class, 'data'])->name('bundle.data');
    Route::get('bundle/product-data', [BundleController::class, 'productData'])->name('bundle.product-data');
    Route::resource('bundle', BundleController::class);

    // Discount
    Route::get('discount/data', [DiscountController::class, 'data'])->name('discount.data');
    Route::post('discount/{discount}/attach-product', [DiscountController::class, 'attachProduct'])->name('discount.attach-product');
    Route::post('discount/{discount}/detach-product', [DiscountController::class, 'detachProduct'])->name('discount.detach-product');
    Route::resource('discount', DiscountController::class);

    // Voucher
    Route::get('voucher/data', [VoucherController::class, 'data'])->name('voucher.data');
    Route::resource('voucher', VoucherController::class);

    // Order — New order
    Route::get('order/data', [OrderController::class, 'data'])->name('order.data');
    Route::get('order', [OrderController::class, 'index'])->name('order.index');
    Route::post('order/store-cart', [OrderController::class, 'storeCart'])->name('order.store-cart');
    Route::get('order/create', [OrderController::class, 'create'])->name('order.create');
    Route::post('order', [OrderController::class, 'store'])->name('order.store');
    Route::post('order/check-voucher', [OrderController::class, 'checkVoucher'])->name('order.check-voucher');
    Route::get('order/bundle-data', [OrderController::class, 'bundleData'])->name('order.bundle-data');
    // Order — List & detail
    Route::get('order/list', [OrderController::class, 'list'])->name('order.list');
    Route::get('order/list-data', [OrderController::class, 'listData'])->name('order.list-data');
    Route::post('order/{order}/complete', [OrderController::class, 'complete'])->name('order.complete');
    Route::post('order/{order}/accept', [OrderController::class, 'accept'])->name('order.accept');
    Route::get('order/{order}/receipt', [OrderController::class, 'receipt'])->name('order.receipt');
    Route::get('order/{order}', [OrderController::class, 'show'])->name('order.show');

    // Transaction
    Route::get('transaction/data', [TransactionController::class, 'data'])->name('transaction.data');
    Route::get('transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('transaction/{transaction}', [TransactionController::class, 'show'])->name('transaction.show');

    // History
    Route::prefix('history')->name('history.')->group(function () {
        Route::get('stock', [HistoryController::class, 'stockIndex'])->name('stock.index');
        Route::get('stock/data', [HistoryController::class, 'stockData'])->name('stock.data');
        Route::get('stock/{id}', [HistoryController::class, 'stockShow'])->name('stock.show');
        Route::get('discount', [HistoryController::class, 'discountIndex'])->name('discount.index');
        Route::get('discount/data', [HistoryController::class, 'discountData'])->name('discount.data');
        Route::get('discount/{id}', [HistoryController::class, 'discountShow'])->name('discount.show');
        Route::get('bundle', [HistoryController::class, 'bundleIndex'])->name('bundle.index');
        Route::get('bundle/data', [HistoryController::class, 'bundleData'])->name('bundle.data');
        Route::get('bundle/{id}', [HistoryController::class, 'bundleShow'])->name('bundle.show');
        Route::get('product', [HistoryController::class, 'productIndex'])->name('product.index');
        Route::get('product/data', [HistoryController::class, 'productData'])->name('product.data');
        Route::get('product/{id}', [HistoryController::class, 'productShow'])->name('product.show');
        Route::get('voucher', [HistoryController::class, 'voucherIndex'])->name('voucher.index');
        Route::get('voucher/data', [HistoryController::class, 'voucherData'])->name('voucher.data');
        Route::get('voucher/{id}', [HistoryController::class, 'voucherShow'])->name('voucher.show');
    });

    // Keuangan (COGS & HPP Decoupled)
    Route::prefix('keuangan')->name('keuangan.')->group(function () {
        Route::get('cogs-raw-material/data', [CogsRawMaterialController::class, 'data'])->name('cogs-raw-material.data');
        Route::post('cogs-raw-material/{id}/opname', [CogsRawMaterialController::class, 'opname'])->name('cogs-raw-material.opname');
        Route::resource('cogs-raw-material', CogsRawMaterialController::class);

        Route::get('cogs-recipe/data', [CogsRecipeController::class, 'data'])->name('cogs-recipe.data');
        Route::resource('cogs-recipe', CogsRecipeController::class);

        Route::get('cogs-waste/data', [CogsWasteLogController::class, 'data'])->name('cogs-waste.data');
        Route::resource('cogs-waste', CogsWasteLogController::class);

        // Purchase Order + Receiving (Keuangan & Raw Stock)
        Route::get('purchase-order/data', [PurchaseOrderController::class, 'data'])->name('purchase-order.data');
        Route::post('purchase-order/{purchase_order}/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-order.confirm');
        Route::post('purchase-order/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-order.cancel');
        Route::post('purchase-order/{purchase_order}/return', [PurchaseOrderController::class, 'return'])->name('purchase-order.return');
        Route::get('purchase-order/{purchase_order}/receiving/create', [PurchaseOrderController::class, 'receivingCreate'])->name('purchase-order.receiving.create');
        Route::post('purchase-order/{purchase_order}/receiving', [PurchaseOrderController::class, 'receivingStore'])->name('purchase-order.receiving.store');
        Route::resource('purchase-order', PurchaseOrderController::class);

        Route::get('hpp-report', [HppReportController::class, 'index'])->name('hpp-report.index');
        Route::post('hpp-report/operational', [HppReportController::class, 'storeOperational'])->name('hpp-report.store-operational');

        Route::get('menu-analytics', [MenuAnalyticsController::class, 'index'])->name('menu-analytics.index');
    });

    Route::get('guide', function () {
        return view('admin.guide.index');
    })->name('guide.index');
});

Route::prefix('sys_admin')->name('sys_admin.')->group(function () {
    Route::get('company/data', [CompanyController::class, 'data'])->name('company.data');
    Route::resource('company', CompanyController::class);
});

// ===================== GUEST (QR Ordering) =====================
use App\Http\Controllers\Guest\OrderController as GuestOrderController;

Route::prefix('guest')->name('guest.')->group(function () {
    // Menu (QR meja)
    Route::get('/menu/{table_id}', [GuestOrderController::class, 'index'])->name('index');
    // Review & checkout
    Route::post('/checkout', [GuestOrderController::class, 'checkout'])->name('checkout');
    Route::get('/review/{table_id}', [GuestOrderController::class, 'review'])->name('review');
    Route::post('/submit', [GuestOrderController::class, 'submit'])->name('submit');
    // Status pesanan per meja
    Route::get('/status/{table_id}', [GuestOrderController::class, 'status'])->name('status');
    // Cek voucher (AJAX)
    Route::post('/check-voucher', [GuestOrderController::class, 'checkVoucher'])->name('check-voucher');
});
