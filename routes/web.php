<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json(['status' => 'ok']);
    } catch (Exception $e) {
        return response()->json(['status' => 'db_error'], 200);
    }
});
Route::get('/', function () {
    return request()->user() !== null
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// ==========================================
// ក្រុមទី ១៖ គ្រប់គ្នាដែលបាន Login រួចអាចប្រើប្រាស់បាន (Admin, Staff, Cashier)
// ==========================================
Route::middleware(['auth', 'shop.active'])->group(function () {

    // ទំព័រ Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ប្រព័ន្ធលក់ទំនិញ (POS)
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

    // គ្រប់គ្រងព័ត៌មានអតិថិជន (Cashier ត្រូវការចុះឈ្មោះអតិថិជនពេលលក់ទំនិញ)
    Route::get('/customers/export/excel', [ExportController::class, 'customersExcel'])->name('customers.export.excel');
    Route::get('/customers/debts/pdf', [ExportController::class, 'debtPdf'])->name('customers.debts.pdf');
    Route::resource('customers', CustomerController::class);

    // ប្រវត្តិរូបផ្ទាល់ខ្លួន (Profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

// ==========================================
// ក្រុមទី ២៖ Staff/Cashier អាចមើល Orders/Reprint; Owner មានសិទ្ធិគ្រប់គ្រងទិន្នន័យ
// ==========================================
Route::middleware(['auth', 'shop.active', 'role:staff,cashier,owner'])->group(function () {
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}/invoice/pdf', [OrderController::class, 'invoicePdf'])
        ->name('orders.invoice.pdf');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])
        ->name('orders.invoice');
});

Route::middleware(['auth', 'shop.active', 'role:staff,owner'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/pdf', [ExportController::class, 'pdf'])
        ->name('reports.pdf');

    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])
        ->name('reports.excel');

    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])
        ->name('reports.export.excel');

});

Route::middleware(['auth', 'shop.active', 'role:owner'])->group(function () {
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products/export/excel', [ExportController::class, 'productsExcel'])->name('products.export.excel');
    Route::get('products/labels/pdf', [ExportController::class, 'productLabelsPdf'])->name('products.labels.pdf');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);

    Route::get('orders/export/excel', [OrderController::class, 'exportExcel'])
        ->name('orders.export.excel');

    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
});

// ==========================================
// ក្រុមទី ៣៖ សម្រាប់តែ Admin តែម្នាក់គត់ (Staff និង Cashier មិនអាចចូលបានទេ)
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::resource('users', UserController::class);
    Route::resource('shops', ShopController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::patch('/shops/{shop}/suspend', [ShopController::class, 'suspend'])->name('shops.suspend');

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups/create', [BackupController::class, 'create'])->name('backups.create');
    Route::get('/backups/download/{filename}', [BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/restore/{filename}', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/delete/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');

});

Route::middleware(['auth', 'shop.active', 'role:owner'])->group(function () {
    Route::resource('staff', StaffController::class)->except(['show']);
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
require __DIR__.'/auth.php';
