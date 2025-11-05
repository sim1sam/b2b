<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Vendors
    Route::resource('vendors', \App\Http\Controllers\Admin\VendorController::class)->names([
        'index' => 'admin.vendors.index',
        'create' => 'admin.vendors.create',
        'store' => 'admin.vendors.store',
        'show' => 'admin.vendors.show',
        'edit' => 'admin.vendors.edit',
        'update' => 'admin.vendors.update',
        'destroy' => 'admin.vendors.destroy',
    ]);
    
    // Fund Management
    Route::resource('funds', \App\Http\Controllers\Admin\FundController::class)->names([
        'index' => 'admin.funds.index',
        'show' => 'admin.funds.show',
        'edit' => 'admin.funds.edit',
        'update' => 'admin.funds.update',
        'destroy' => 'admin.funds.destroy',
    ]);
    Route::post('funds/{fundTransaction}/approve', [\App\Http\Controllers\Admin\FundController::class, 'approve'])->name('admin.funds.approve');
    Route::post('funds/{fundTransaction}/reject', [\App\Http\Controllers\Admin\FundController::class, 'reject'])->name('admin.funds.reject');
    
    // Clients Management
    Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update'])->names([
        'index' => 'admin.clients.index',
        'create' => 'admin.clients.create',
        'store' => 'admin.clients.store',
        'show' => 'admin.clients.show',
        'edit' => 'admin.clients.edit',
        'update' => 'admin.clients.update',
    ]);
    
    // Purchase Requests Management
    Route::resource('purchase-requests', \App\Http\Controllers\Admin\PurchaseRequestController::class)->names([
        'index' => 'admin.purchase-requests.index',
        'create' => 'admin.purchase-requests.create',
        'store' => 'admin.purchase-requests.store',
        'show' => 'admin.purchase-requests.show',
        'edit' => 'admin.purchase-requests.edit',
        'update' => 'admin.purchase-requests.update',
        'destroy' => 'admin.purchase-requests.destroy',
    ]);
    Route::post('purchase-requests/{id}/mark-as-paid', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'markAsPaid'])->name('admin.purchase-requests.mark-as-paid');
    Route::get('purchase-requests/{id}/received-items', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'showReceivedItemsForm'])->name('admin.purchase-requests.received-items');
    Route::post('purchase-requests/{id}/received-items', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'storeReceivedItems'])->name('admin.purchase-requests.store-received-items');
    Route::get('purchase-requests/{id}/provisional-invoice', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'showProvisionalInvoice'])->name('admin.purchase-requests.provisional-invoice');
    Route::put('purchase-requests/{id}/shipping-mark', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'updateShippingMark'])->name('admin.purchase-requests.update-shipping-mark');

    // Shipping Marks Management
    Route::get('shipping-marks', [\App\Http\Controllers\Admin\ShippingMarkController::class, 'index'])->name('admin.shipping-marks.index');
    Route::get('shipping-marks/{mark}', [\App\Http\Controllers\Admin\ShippingMarkController::class, 'show'])->name('admin.shipping-marks.show');

    // Invoices Management
    Route::get('invoices', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('admin.invoices.index');
    Route::get('invoices/{id}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('admin.invoices.show');
    Route::post('invoices/generate/{mark}', [\App\Http\Controllers\Admin\InvoiceController::class, 'generate'])->name('admin.invoices.generate');
    Route::post('invoices/{id}/mark-delivered', [\App\Http\Controllers\Admin\InvoiceController::class, 'markAsDelivered'])->name('admin.invoices.mark-delivered');
    Route::post('invoices/{id}/complete-order', [\App\Http\Controllers\Admin\InvoiceController::class, 'completeOrder'])->name('admin.invoices.complete-order');

    // Disputes Management
    Route::get('disputes', [\App\Http\Controllers\Admin\DisputeController::class, 'index'])->name('admin.disputes.index');
    Route::get('disputes/{id}', [\App\Http\Controllers\Admin\DisputeController::class, 'show'])->name('admin.disputes.show');
    Route::post('disputes/{id}/add-response', [\App\Http\Controllers\Admin\DisputeController::class, 'addResponse'])->name('admin.disputes.add-response');
    Route::post('disputes/{id}/mark-solved', [\App\Http\Controllers\Admin\DisputeController::class, 'markAsSolved'])->name('admin.disputes.mark-solved');

    // Admin Profile
    Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('admin.profile.update');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'edit'])->name('admin.settings.edit');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.settings.update');
    
    // Shipping Charges Management
    Route::resource('shipping-charges', \App\Http\Controllers\Admin\ShippingChargeController::class)->names([
        'index' => 'admin.shipping-charges.index',
        'create' => 'admin.shipping-charges.create',
        'store' => 'admin.shipping-charges.store',
        'show' => 'admin.shipping-charges.show',
        'edit' => 'admin.shipping-charges.edit',
        'update' => 'admin.shipping-charges.update',
        'destroy' => 'admin.shipping-charges.destroy',
    ]);
});

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    
    // Vendors
    Route::resource('vendors', \App\Http\Controllers\Customer\VendorController::class)->names([
        'index' => 'customer.vendors.index',
        'create' => 'customer.vendors.create',
        'store' => 'customer.vendors.store',
        'show' => 'customer.vendors.show',
        'edit' => 'customer.vendors.edit',
        'update' => 'customer.vendors.update',
        'destroy' => 'customer.vendors.destroy',
    ]);
    
    // Fund Management
    Route::resource('funds', \App\Http\Controllers\Customer\FundController::class)->only(['index', 'create', 'store', 'show'])->names([
        'index' => 'customer.funds.index',
        'create' => 'customer.funds.create',
        'store' => 'customer.funds.store',
        'show' => 'customer.funds.show',
    ]);
    
        // Purchase Requests
        Route::resource('purchase-requests', \App\Http\Controllers\Customer\PurchaseRequestController::class)->names([
            'index' => 'customer.purchase-requests.index',
            'create' => 'customer.purchase-requests.create',
            'store' => 'customer.purchase-requests.store',
            'show' => 'customer.purchase-requests.show',
            'edit' => 'customer.purchase-requests.edit',
            'update' => 'customer.purchase-requests.update',
            'destroy' => 'customer.purchase-requests.destroy',
        ]);
        Route::post('purchase-requests/{purchaseRequest}/upload-invoice', [\App\Http\Controllers\Customer\PurchaseRequestController::class, 'uploadInvoice'])->name('customer.purchase-requests.upload-invoice');
        Route::post('purchase-requests/{purchaseRequest}/upload-tracking-id', [\App\Http\Controllers\Customer\PurchaseRequestController::class, 'uploadTrackingId'])->name('customer.purchase-requests.upload-tracking-id');
        Route::get('purchase-requests/{id}/provisional-invoice', [\App\Http\Controllers\Admin\PurchaseRequestController::class, 'showProvisionalInvoice'])->name('customer.purchase-requests.provisional-invoice');
    
    // Invoices Management
    Route::get('invoices', [\App\Http\Controllers\Customer\InvoiceController::class, 'index'])->name('customer.invoices.index');
    Route::get('invoices/{id}', [\App\Http\Controllers\Customer\InvoiceController::class, 'show'])->name('customer.invoices.show');
    Route::post('invoices/{id}/pay', [\App\Http\Controllers\Customer\InvoiceController::class, 'pay'])->name('customer.invoices.pay');
    Route::post('invoices/{id}/delivery-request', [\App\Http\Controllers\Customer\InvoiceController::class, 'requestDelivery'])->name('customer.invoices.delivery-request');
    Route::post('invoices/{id}/submit-dispute', [\App\Http\Controllers\Customer\InvoiceController::class, 'submitDispute'])->name('customer.invoices.submit-dispute');
    Route::post('invoices/{id}/mark-received', [\App\Http\Controllers\Customer\InvoiceController::class, 'markAsReceived'])->name('customer.invoices.mark-received');

    // Disputes Management
    Route::get('disputes', [\App\Http\Controllers\Customer\DisputeController::class, 'index'])->name('customer.disputes.index');
    Route::get('disputes/{id}', [\App\Http\Controllers\Customer\DisputeController::class, 'show'])->name('customer.disputes.show');
    
    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('customer.profile.update');
});
