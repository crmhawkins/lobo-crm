<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control Presupuestario PTO. Patrocinios</title>
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
    <h1>Control PTO. Patrocinios {{ $year }}</h1>
    @foreach ($cajaPorTrimestre as $trimestre => $cajaPorMes)
        <h3>Trimestre {{ $trimestre }}</h3>
        <table class="table">
            <thead>
                <tr class="trimestre-header">
                    <th>Mes</th>
                    @foreach($delegaciones as $delegacion)
                        <th>{{ $delegacion->nombre }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cajaPorMes as $mes => $totalesPorDelegacion)
                    <tr class="mes-header">
                        <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                        @php
                            $totalMesGeneral = 0;
                        @endphp
                        @foreach($delegaciones as $delegacion)
                            @php
                                $totalDelegacion = $totalesPorDelegacion[$delegacion->nombre] ?? 0;
                                $totalMesGeneral += $totalDelegacion;
                            @endphp
                            <td>{{ number_format($totalDelegacion, 2, ',', '.') }}€</td>
                        @endforeach
                        <td>{{ number_format($totalMesGeneral, 2, ',', '.') }}€</td>
                    </tr>
                @endforeach
                <tr class="trimestre-header">
                    <td>Total Trimestre {{ $trimestre }}</td>
                    @php
                        $totalTrimestreGeneral = 0;
                    @endphp
                    @foreach($delegaciones as $delegacion)
                        @php
                            $totalTrimestreDelegacion = array_sum(array_column($cajaPorMes, $delegacion->nombre));
                            $totalTrimestreGeneral += $totalTrimestreDelegacion;
                        @endphp
                        <td>{{ number_format($totalTrimestreDelegacion, 2, ',', '.') }}€</td>
                    @endforeach
                    <td>{{ number_format($totalTrimestreGeneral, 2, ',', '.') }}€</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html> 