<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\DailyInventory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();
        
        // Inventario de hoy
        $inventory = DailyInventory::whereDate('date', $today)->get();
        $totalBaked = $inventory->sum('initial_stock');
        $totalRemaining = $inventory->sum('current_stock');
        $totalSold = $totalBaked - $totalRemaining;
        
        // Ventas por método de pago (hoy)
        $efectivo = Sale::whereDate('sale_date', $today)
            ->where('payment_method', 'efectivo')
            ->sum('total_amount');
        
        $nequi = Sale::whereDate('sale_date', $today)
            ->where('payment_method', 'nequi')
            ->sum('total_amount');
        
        $daviplata = Sale::whereDate('sale_date', $today)
            ->where('payment_method', 'daviplata')
            ->sum('total_amount');
        
        $credito = Sale::whereDate('sale_date', $today)
            ->where('payment_method', 'credito')
            ->where('is_paid', false)
            ->sum('total_amount');

        return view('dashboard', compact(
            'totalBaked', 'totalRemaining', 'totalSold',
            'efectivo', 'nequi', 'daviplata', 'credito', 'inventory'
        ));
    }
}