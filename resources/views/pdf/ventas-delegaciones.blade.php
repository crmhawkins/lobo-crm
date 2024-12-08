<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas por Delegaciones</title>
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
    </style>
</head>
<body>
    <h2 class="text-center">Presupuesto por Ventas delegaciones {{ $year }}</h2>
    <table class="table">
        <thead>
            <tr>
                <th rowspan="2">Delegaciones</th>
                <th colspan="2">1 TRIMESTRE</th>
                <th colspan="2">2 TRIMESTRE</th>
                <th colspan="2">3 TRIMESTRE</th>
                <th colspan="2">4 TRIMESTRE</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Ventas</th>
                <th>%</th>
                <th>Ventas</th>
                <th>%</th>
                <th>Ventas</th>
                <th>%</th>
                <th>Ventas</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($delegaciones as $delegacion)
                @php
                    $ventas = $delegacionVentas[$delegacion->nombre] ?? ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0];
                    $totalDelegacion = $ventas['1T'] + $ventas['2T'] + $ventas['3T'] + $ventas['4T'];

                    $porcentaje1T = $totalVentas['1T'] > 0 ? ($ventas['1T'] / $totalVentas['1T']) * 100 : 0;
                    $porcentaje2T = $totalVentas['2T'] > 0 ? ($ventas['2T'] / $totalVentas['2T']) * 100 : 0;
                    $porcentaje3T = $totalVentas['3T'] > 0 ? ($ventas['3T'] / $totalVentas['3T']) * 100 : 0;
                    $porcentaje4T = $totalVentas['4T'] > 0 ? ($ventas['4T'] / $totalVentas['4T']) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ $delegacion->nombre }}</td>
                    <td>{{ number_format($ventas['1T'], 2, ',', '.') }} €</td>
                    <td>{{ number_format($porcentaje1T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($ventas['2T'], 2, ',', '.') }} €</td>
                    <td>{{ number_format($porcentaje2T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($ventas['3T'], 2, ',', '.') }} €</td>
                    <td>{{ number_format($porcentaje3T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($ventas['4T'], 2, ',', '.') }} €</td>
                    <td>{{ number_format($porcentaje4T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($totalDelegacion, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
            <tr>
                <td>Total</td>
                <td>{{ number_format($totalVentas['1T'], 2, ',', '.') }} €</td>
                <td>100.00 %</td>
                <td>{{ number_format($totalVentas['2T'], 2, ',', '.') }} €</td>
                <td>100.00 %</td>
                <td>{{ number_format($totalVentas['3T'], 2, ',', '.') }} €</td>
                <td>100.00 %</td>
                <td>{{ number_format($totalVentas['4T'], 2, ',', '.') }} €</td>
                <td>100.00 %</td>
                <td>{{ number_format($totalVentas['anual'], 2, ',', '.') }} €</td>
            </tr>
        </tbody>
    </table>
</body>
</html> 