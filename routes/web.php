<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestController;
use App\Livewire\ClientLive;
use App\Livewire\LedgerLive;
use App\Livewire\OrderLive;
use App\Livewire\PaymentLive;
use App\Livewire\ProductLive;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin', [AdminController::class, 'admin'])->name('admin');
    Route::post('/admin', [AdminController::class, 'register'])->name('admin.register');

    Route::get('/clients', ClientLive::class)->name('clients');

    Route::get('/products', ProductLive::class)->name('products');

    Route::get('/orders', OrderLive::class)->name('orders');

    Route::get('/payments', PaymentLive::class)->name('payment');

    Route::get('/ledger', LedgerLive::class)->name('ledger');

    Route::get('/orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');

    Route::get('/ledger/print', [LedgerController::class, 'print'])->name('ledger.print');

    Route::get('/asd', [LedgerController::class, 'asd'])->name('ledger.print');
    
    Route::get('/request', [RequestController::class, 'request'])->name('request');
});

require __DIR__.'/auth.php';
