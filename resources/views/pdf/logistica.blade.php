<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Logística</title>
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
    <h1>Gastos de Transporte por Logística - Año {{ $year }}</h1>
    @foreach ($gastosTransportePorTrimestre as $trimestre => $gastosPorMes)
        <h3>Trimestre {{ $trimestre }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    @foreach($delegaciones as $delegacion)
                        <th>{{ $delegacion->nombre }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($gastosPorMes as $mes => $gastosPorDelegacion)
                    <tr>
                        <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                        @foreach($delegaciones as $delegacion)
                            <td>{{ number_format($gastosPorDelegacion[$delegacion->nombre] ?? 0, 2, ',', '.') }}€</td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td>Total del Trimestre</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($totalPorTrimestre[$trimestre][$delegacion->nombre] ?? 0, 2, ',', '.') }}€</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html> 