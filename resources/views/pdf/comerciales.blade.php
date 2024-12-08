<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Comerciales</title>
    <style>
        body {
            font-size: 12px;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Control PTO. COMERCIALES {{ $year }}</h1>
    @foreach ($ventasPorTrimestre as $trimestre => $ventasPorMes)
        <h3>Trimestre {{ $trimestre }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    @foreach($delegaciones as $delegacion)
                        <th>{{ $delegacion->nombre }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ventasPorMes as $mes => $productos)
                    <tr>
                        <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                        @foreach($delegaciones as $delegacion)
                            @php
                                $totalVentasDelegacion = array_reduce($productos, function ($carry, $producto) use ($delegacion) {
                                    return $carry + ($producto['ventasDelegaciones'][$delegacion->nombre]['costeTotal'] ?? 0);
                                }, 0);
                            @endphp
                            <td>{{ number_format($totalVentasDelegacion, 2, ',', '.') }}€</td>
                        @endforeach
                        <td>{{ number_format(array_sum(array_column($productos, 'costeTotal')), 2, ',', '.') }}€</td>
                    </tr>
                @endforeach
                <tr>
                    <td>Total Trimestre {{ $trimestre }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format(array_sum(array_column($productos, 'costeTotal')), 2, ',', '.') }}€</td>
                    @endforeach
                    <td>{{ number_format(array_sum(array_column($productos, 'costeTotal')), 2, ',', '.') }}€</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html> 