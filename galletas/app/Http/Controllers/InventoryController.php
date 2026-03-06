<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\DailyInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    // Mostrar formulario de inventario
    public function index()
    {
        $products = Product::all();
        
        // Buscar inventario de hoy si existe
        $todayInventory = DailyInventory::whereDate('date', today())
            ->get()
            ->keyBy('product_id');
        
        return view('inventory.index', compact('products', 'todayInventory'));
    }

    // Guardar inventario del día
    public function store(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*' => 'integer|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            foreach ($request->products as $productId => $quantity) {
                DailyInventory::updateOrCreate(
                    [
                        'product_id' => $productId,
                        'date' => today(),
                    ],
                    [
                        'initial_stock' => $quantity,
                        'current_stock' => $quantity,
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('inventory.index')
                ->with('success', '✅ Inventario actualizado correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }
}