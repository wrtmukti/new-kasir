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
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\BundleController;

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

    // Purchase Order + Receiving
    Route::get('purchase-order/data', [PurchaseOrderController::class, 'data'])->name('purchase-order.data');
    Route::post('purchase-order/{purchase_order}/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-order.confirm');
    Route::post('purchase-order/{purchase_order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-order.cancel');
    Route::post('purchase-order/{purchase_order}/return', [PurchaseOrderController::class, 'return'])->name('purchase-order.return');
    Route::get('purchase-order/{purchase_order}/receiving/create', [PurchaseOrderController::class, 'receivingCreate'])->name('purchase-order.receiving.create');
    Route::post('purchase-order/{purchase_order}/receiving', [PurchaseOrderController::class, 'receivingStore'])->name('purchase-order.receiving.store');
    Route::resource('purchase-order', PurchaseOrderController::class);

    // Bundle
    Route::get('bundle/data', [BundleController::class, 'data'])->name('bundle.data');
    Route::resource('bundle', BundleController::class);
});

Route::prefix('sys_admin')->name('sys_admin.')->group(function () {
    Route::get('company/data', [CompanyController::class, 'data'])->name('company.data');
    Route::resource('company', CompanyController::class);
});
