<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DailyInventory;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    // Mostrar formulario de nueva venta
    public function create()
    {
        $products = Product::all();
        
        // Obtener inventario de hoy con stock disponible
        $todayInventory = DailyInventory::whereDate('date', today())
            ->where('current_stock', '>', 0)
            ->get()
            ->keyBy('product_id');
        
        return view('sales.create', compact('products', 'todayInventory'));
    }

    // Procesar y guardar la venta
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:efectivo,nequi,daviplata,credito',
            'customer_name' => 'required_if:payment_method,credito|string|max:100',
            'customer_phone' => 'required_if:payment_method,credito|string|max:20',
        ]);

        DB::beginTransaction();
        
        try {
            $totalAmount = 0;
            $saleItems = [];

            // Calcular total y preparar items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];
                
                // Verificar stock disponible
                $inventory = DailyInventory::whereDate('date', today())
                    ->where('product_id', $product->id)
                    ->first();
                    
                if (!$inventory || $inventory->current_stock < $quantity) {
                    throw new \Exception("No hay stock suficiente para: {$product->name}");
                }
                
                $subtotal = $product->price * $quantity;
                $totalAmount += $subtotal;
                
                $saleItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_at_moment' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            // Crear la venta
            $sale = Sale::create([
                'customer_name' => $request->payment_method === 'credito' ? $request->customer_name : 'Cliente General',
                'customer_phone' => $request->payment_method === 'credito' ? $request->customer_phone : '',
                'payment_method' => $request->payment_method,
                'total_amount' => $totalAmount,
                'is_paid' => $request->payment_method !== 'credito',
                'sale_date' => today(),
            ]);

            // Guardar items y descontar inventario
            foreach ($saleItems as $itemData) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price_at_moment' => $itemData['price_at_moment'],
                ]);

                // Descontar del inventario diario
                DailyInventory::whereDate('date', today())
                    ->where('product_id', $itemData['product_id'])
                    ->decrement('current_stock', $itemData['quantity']);
            }

            DB::commit();
            
            return redirect()->route('dashboard')
                ->with('success', '✅ Venta registrada correctamente');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    // Mostrar créditos pendientes
    public function credits()
    {
        $pendingCredits = Sale::where('payment_method', 'credito')
            ->where('is_paid', false)
            ->with('payments')
            ->orderBy('sale_date', 'desc')
            ->get();
            
        return view('sales.credits', compact('pendingCredits'));
    }

    // Registrar abono a crédito
    public function payCredit(Request $request, $saleId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:efectivo,nequi,daviplata',
        ]);

        $sale = Sale::findOrFail($saleId);
        
        // Registrar el pago
        Payment::create([
            'sale_id' => $sale->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => today(),
        ]);

        // Verificar si ya pagó todo
        $totalPaid = $sale->payments->sum('amount');
        if ($totalPaid >= $sale->total_amount) {
            $sale->update(['is_paid' => true]);
        }

        return redirect()->route('sales.credits')
            ->with('success', '✅ Abono registrado correctamente');
    }
}