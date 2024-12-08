<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Análisis de Ventas</title>
    <style>
        body {
            font-size: 12px;
            color: #000;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .trimestre-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .mes-header {
            font-weight: bold;
            background-color: grey;
            text-align: center;
        }
    </style>
</head>
<body>
    <h2 class="text-center">Análisis de Ventas - Trimestre {{ $trimestre }} - Año {{ $year }}</h2>

    <!-- Tabla general de ventas por delegación -->
    <h3>Ventas por Delegación</h3>
    <table class="table">
        <thead>
            <tr class="trimestre-header">
                <th>Delegaciones</th>
                @foreach($meses as $mes)
                    <th>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</th>
                @endforeach
                <th>Total</th>
                <th>% Venta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delegaciones as $delegacion)
                <tr>
                    <td>{{ $delegacion->nombre }}</td>
                    @foreach($meses as $mes)
                        <td>{{ number_format($ventasPorDelegacion[$mes][$delegacion->nombre], 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format($totalesPorDelegacion[$delegacion->nombre], 2, ',', '.') }} €</td>
                    <td>{{ number_format($porcentajeVentasPorDelegacion[$delegacion->nombre], 2, ',', '.') }} %</td>
                </tr>
            @endforeach

            <!-- Fila para los totales generales -->
            <tr class="trimestre-header">
                <td><strong>Total General</strong></td>
                @foreach($meses as $mes)
                    <td><strong>{{ number_format(array_sum(array_column($ventasPorDelegacion, $mes)), 2, ',', '.') }} €</strong></td>
                @endforeach
                <td><strong>{{ number_format($totalGeneralVentas, 2, ',', '.') }} €</strong></td>
                <td><strong>100%</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Tablas por producto -->
    @foreach($productos as $producto)
    <h3>{{ $producto->nombre }}</h3>
    <table class="table">
        <thead>
            <tr class="trimestre-header">
                <th>Delegaciones</th>
                @foreach($meses as $mes)
                    <th>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delegaciones as $delegacion)
                <tr>
                    <td>{{ $delegacion->nombre }}</td>
                    @foreach($meses as $mes)
                        <td>{{ $ventasPorProducto[$producto->id][$mes][$delegacion->nombre] }}</td>
                    @endforeach
                    <td>{{ array_sum(array_column($ventasPorProducto[$producto->id], $delegacion->nombre)) }}</td>
                </tr>
            @endforeach

            <!-- Fila para los totales generales por producto -->
            <tr class="trimestre-header">
                <td><strong>Total General</strong></td>
                @foreach($meses as $mes)
                    <td><strong>{{ array_sum(array_column($ventasPorProducto[$producto->id], $mes)) }}</strong></td>
                @endforeach
                <td><strong>{{ array_sum($totalesPorProducto[$producto->id]) }}</strong></td>
            </tr>
        </tbody>
    </table>
    @endforeach
</body>
</html> 