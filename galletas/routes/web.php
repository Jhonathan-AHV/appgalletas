<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\DashboardController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Inventario
Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');

// Ventas
Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');

// Créditos
Route::get('/sales/credits', [SaleController::class, 'credits'])->name('sales.credits');
Route::post('/sales/{sale}/pay', [SaleController::class, 'payCredit'])->name('sales.payCredit');