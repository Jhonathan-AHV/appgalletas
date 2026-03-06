<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Galletas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">📊 Dashboard del Día - {{ today()->format('d/m/Y') }}</h2>

        <!-- Tarjetas de Resumen -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>💵 Efectivo</h5>
                        <h3>${{ number_format($efectivo, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>📱 Nequi</h5>
                        <h3>${{ number_format($nequi, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>💳 Daviplata</h5>
                        <h3>${{ number_format($daviplata, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5>💰 Por Cobrar (Crédito)</h5>
                        <h3>${{ number_format($credito, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventario -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>📦 Inventario del Día</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Horneadas:</strong> {{ $totalBaked }}
                    </div>
                    <div class="col-md-4">
                        <strong>Vendidas:</strong> {{ $totalSold }}
                    </div>
                    <div class="col-md-4">
                        <strong>Restantes:</strong> {{ $totalRemaining }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Navegación -->
        <div class="row">
            <div class="col-md-4">
                <a href="{{ route('inventory.index') }}" class="btn btn-primary w-100">📦 Gestionar Inventario</a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('sales.create') }}" class="btn btn-success w-100">💰 Nueva Venta</a>
            </div>
            <div class="col-md-4">
                <a href="#" class="btn btn-secondary w-100">💳 Ver Créditos</a>
            </div>
        </div>
    </div>
</body>
</html>