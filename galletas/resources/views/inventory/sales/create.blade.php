<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - Galletas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4>💰 Nueva Venta</h4>
            </div>
            <div class="card-body">
                
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                    @csrf
                    
                    <!-- Tabla de productos -->
                    <table class="table table-bordered mb-4">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock Hoy</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="productsTable">
                            @foreach($products as $product)
                                @php
                                    $inventory = $todayInventory->get($product->id);
                                    $stock = $inventory ? $inventory->current_stock : 0;
                                @endphp
                                <tr class="product-row" data-product-id="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $stock }}">
                                    <td>{{ $product->name }}</td>
                                    <td>${{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>
                                        @if($stock > 0)
                                            <span class="badge bg-success">{{ $stock }}</span>
                                        @else
                                            <span class="badge bg-danger">Agotado</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($stock > 0)
                                            <input type="number" 
                                                   name="items[{{ $product->id }}][quantity]" 
                                                   class="form-control quantity-input" 
                                                   min="1" 
                                                   max="{{ $stock }}" 
                                                   value="0"
                                                   data-product-id="{{ $product->id }}">
                                        @else
                                            <input type="number" class="form-control" disabled value="0">
                                        @endif
                                    </td>
                                    <td class="subtotal">$0</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-btn" {{ $stock <= 0 ? 'disabled' : '' }}>
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Total -->
                    <div class="d-flex justify-content-end mb-4">
                        <h4>Total: $<span id="grandTotal">0</span></h4>
                    </div>

                    <!-- Método de pago -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Método de Pago:</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="efectivo" value="efectivo" checked>
                                <label class="form-check-label" for="efectivo">💵 Efectivo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="nequi" value="nequi">
                                <label class="form-check-label" for="nequi">📱 Nequi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="daviplata" value="daviplata">
                                <label class="form-check-label" for="daviplata">💳 Daviplata</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="credito" value="credito">
                                <label class="form-check-label" for="credito">📝 Crédito</label>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del cliente (solo para crédito) -->
                    <div id="creditFields" class="d-none mb-4 p-3 bg-warning bg-opacity-25 rounded">
                        <h6 class="fw-bold">👤 Datos del Cliente (Crédito)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Nombre:</label>
                                <input type="text" name="customer_name" class="form-control" placeholder="Nombre completo">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Teléfono:</label>
                                <input type="tel" name="customer_phone" class="form-control" placeholder="300 123 4567">
                            </div>
                        </div>
                        <small class="text-muted">* Estos datos son obligatorios para ventas a crédito</small>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100">
                        ✅ Registrar Venta
                    </button>
                </form>
            </div>
        </div>

        <!-- Botones de navegación -->
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">📊 Dashboard</a>
            <a href="{{ route('inventory.index') }}" class="btn btn-primary">📦 Inventario</a>
            <a href="{{ route('sales.credits') }}" class="btn btn-warning">💳 Ver Créditos</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calcular subtotales y total en tiempo real
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', calculateTotals);
        });

        function calculateTotals() {
            let grandTotal = 0;
            
            document.querySelectorAll('.product-row').forEach(row => {
                const quantityInput = row.querySelector('.quantity-input');
                const price = parseFloat(row.dataset.price);
                const quantity = parseInt(quantityInput.value) || 0;
                const stock = parseInt(row.dataset.stock);
                
                // Validar que no exceda el stock
                if (quantity > stock) {
                    quantityInput.value = stock;
                }
                
                const subtotal = price * quantity;
                row.querySelector('.subtotal').textContent = '$' + subtotal.toLocaleString('es-CO');
                grandTotal += subtotal;
            });
            
            document.getElementById('grandTotal').textContent = grandTotal.toLocaleString('es-CO');
        }

        // Mostrar/ocultar campos de crédito
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const creditFields = document.getElementById('creditFields');
                if (this.value === 'credito') {
                    creditFields.classList.remove('d-none');
                    creditFields.querySelector('input[name="customer_name"]').required = true;
                    creditFields.querySelector('input[name="customer_phone"]').required = true;
                } else {
                    creditFields.classList.add('d-none');
                    creditFields.querySelector('input[name="customer_name"]').required = false;
                    creditFields.querySelector('input[name="customer_phone"]').required = false;
                }
            });
        });

        // Eliminar producto (resetear cantidad)
        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('.product-row');
                const input = row.querySelector('.quantity-input');
                input.value = 0;
                calculateTotals();
            });
        });
    </script>
</body>
</html>