<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto por Delegación</title>
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
    <h2>Presupuesto por Delegación {{ $delegacion->nombre ?? 'General' }} - {{ $year }}</h2>

    @foreach ($presupuestosPorTrimestre as $trimestre => $meses)
        <h3>Trimestre {{ $trimestre }}</h3>
        <table class="table">
            <thead>
                <tr class="trimestre-header">
                    <th>Producto</th>
                    <th>Unidades Vendidas</th>
                    <th>Precio Total</th>
                    <th>Coste Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($meses as $mes => $productos)
                    @foreach ($productos as $producto => $data)
                        @foreach ($data['ventasDelegaciones'] as $delegacionNombre => $ventas)
                            <tr>
                                <td>{{ $producto }}</td>
                                <td>{{ $ventas['unidadesVendidas'] }}</td>
                                <td>{{ number_format($ventas['precioTotal'], 2, ',', '.') }} €</td>
                                <td>{{ number_format($ventas['costeTotal'], 2, ',', '.') }} €</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endforeach

    <h3>Resumen Anual</h3>
    <table class="table">
        <thead>
            <tr class="trimestre-header">
                <th>Trimestre</th>
                <th>Ventas Totales</th>
                <th>Compras Totales</th>
                <th>Margen de Beneficio</th>
                <th>% Margen de Beneficio</th>
            </tr>
        </thead>
        <tbody>
            @foreach (['1T', '2T', '3T', '4T', 'anual'] as $periodo)
                <tr>
                    <td>{{ $periodo }}</td>
                    <td>{{ number_format($totalVentas[$periodo], 2, ',', '.') }} €</td>
                    <td>{{ number_format($totalCompras[$periodo], 2, ',', '.') }} €</td>
                    <td>{{ number_format($margenBeneficioReal[$periodo], 2, ',', '.') }} €</td>
                    <td>{{ number_format($margenPorcentajeReal[$periodo], 2, ',', '.') }} %</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Gastos Estructurales</h3>
    <table class="table">
        <thead>
            <tr class="trimestre-header">
                <th>Cuenta</th>
                <th>Nombre</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gastosEstructuralesAnuales as $cuenta => $total)
                <tr>
                    <td>{{ $cuenta }}</td>
                    <td>{{ $gastosEstructuralesPorTrimestre[1][$cuenta]['nombre'] ?? 'N/A' }}</td>
                    <td>{{ number_format($total, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Gastos Variables</h3>
    <table class="table">
        <thead>
            <tr class="trimestre-header">
                <th>Cuenta</th>
                <th>Nombre</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gastosVariablesAnuales as $cuenta => $total)
                <tr>
                    <td>{{ $cuenta }}</td>
                    <td>{{ $gastosVariablesPorTrimestre[1][$cuenta]['nombre'] ?? 'N/A' }}</td>
                    <td>{{ number_format($total, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 