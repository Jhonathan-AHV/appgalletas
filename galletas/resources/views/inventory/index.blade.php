<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario Diario - Galletas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>🍪 Inventario Diario - {{ today()->format('d/m/Y') }}</h4>
            </div>
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf
                    
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad Horneada Hoy</th>
                                <th>Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @php
                                    $inventory = $todayInventory->get($product->id);
                                    $currentStock = $inventory ? $inventory->current_stock : 0;
                                    $initialStock = $inventory ? $inventory->initial_stock : 0;
                                @endphp
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>${{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="number" 
                                               name="products[{{ $product->id }}]" 
                                               class="form-control" 
                                               value="{{ old('products.' . $product->id, $initialStock) }}" 
                                               min="0"
                                               required>
                                    </td>
                                    <td>
                                        @if($currentStock > 0)
                                            <span class="badge bg-success">{{ $currentStock }} disponibles</span>
                                        @else
                                            <span class="badge bg-danger">Sin stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        📦 Actualizar Inventario
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">📊 Ver Dashboard</a>
            <a href="{{ route('sales.create') }}" class="btn btn-success">💰 Nueva Venta</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>