<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créditos Pendientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3 class="mb-4">💳 Créditos Pendientes</h3>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($pendingCredits->isEmpty())
            <div class="alert alert-info">✅ No hay créditos pendientes</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered bg-white">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Pagado</th>
                            <th>Restante</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingCredits as $sale)
                            @php
                                $paid = $sale->payments->sum('amount');
                                $pending = $sale->total_amount - $paid;
                            @endphp
                            <tr>
                                <td>{{ $sale->customer_name }}</td>
                                <td>{{ $sale->customer_phone }}</td>
                                <td>{{ $sale->sale_date->format('d/m/Y') }}</td>
                                <td>${{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                <td>${{ number_format($paid, 0, ',', '.') }}</td>
                                <td class="text-danger fw-bold">${{ number_format($pending, 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#payModal{{ $sale->id }}">
                                        💰 Abonar
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal para abonar -->
                            <div class="modal fade" id="payModal{{ $sale->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('sales.payCredit', $sale->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Abonar a {{ $sale->customer_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Deuda total:</strong> ${{ number_format($sale->total_amount, 0, ',', '.') }}</p>
                                                <p><strong>Ya pagado:</strong> ${{ number_format($paid, 0, ',', '.') }}</p>
                                                <p class="text-danger"><strong>Restante:</strong> ${{ number_format($pending, 0, ',', '.') }}</p>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Monto a abonar:</label>
                                                    <input type="number" name="amount" class="form-control" min="1" max="{{ $pending }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Forma de pago del abono:</label>
                                                    <select name="payment_method" class="form-select" required>
                                                        <option value="efectivo">💵 Efectivo</option>
                                                        <option value="nequi">📱 Nequi</option>
                                                        <option value="daviplata">💳 Daviplata</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-success">✅ Registrar Abono</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <a href="{{ route('dashboard') }}" class="btn btn-secondary mt-3">← Volver al Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>