<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control Presupuestario</title>
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
    <h1>Control Presupuestario</h1>

    <!-- Tabla de Ventas -->
    <h2>Ventas (A)</h2>
    <table>
        <thead>
            <tr>
                <th>Trimestre</th>
                @foreach ($delegaciones as $delegacion)
                    @if($delegacion->nombre != '00 GENERAL GLOBAL')
                        <th>{{ $delegacion->nombre }}</th>
                    @endif
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($totalesPorDelegacionYTrimestre as $trimestre => $totalesDelegacion)
                <tr>
                    <td><b>{{ $trimestre }}</b></td>
                    @foreach ($delegaciones as $delegacion)
                        @if($delegacion->nombre != '00 GENERAL GLOBAL')
                            @php
                                $totalTrimestreDelegacion = $totalesDelegacion[$delegacion->COD] ?? 0;
                            @endphp
                            <td>{{ number_format($totalTrimestreDelegacion, 2) }}€</td>
                        @endif
                    @endforeach
                    @php
                        $totalTrimestreGeneral = $totalesDelegacion['total_general'] ?? 0;
                    @endphp
                    <td><b>{{ number_format($totalTrimestreGeneral, 2) }}€</b></td>
                </tr>
            @endforeach
            <tr>
                <td><b>Total Anual</b></td>
                @foreach ($delegaciones as $delegacion)
                    @if($delegacion->nombre != '00 GENERAL GLOBAL')
                        @php
                            $totalAnualDelegacion = 0;
                            foreach ($totalesPorDelegacionYTrimestre as $totalesDelegacion) {
                                $totalAnualDelegacion += $totalesDelegacion[$delegacion->COD] ?? 0;
                            }
                        @endphp
                        <td><b>{{ number_format($totalAnualDelegacion, 2) }}€</b></td>
                    @endif
                @endforeach
                @php
                    $totalAnualGeneral = 0;
                    foreach ($totalesPorDelegacionYTrimestre as $totalesDelegacion) {
                        $totalAnualGeneral += $totalesDelegacion['total_general'] ?? 0;
                    }
                @endphp
                <td><b>{{ number_format($totalAnualGeneral, 2) }}€</b></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Compras -->
    <h2>Compras (B)</h2>
    <table>
        <thead>
            <tr>
                <th>Trimestre</th>
                @foreach ($delegaciones as $delegacion)
                    @if($delegacion->nombre != '00 GENERAL GLOBAL')
                        <th>{{ $delegacion->nombre }}</th>
                    @endif
                @endforeach
                <th>Total General</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($totalesPorDelegacionYTrimestreCompras as $trimestre => $totalesDelegacion)
                <tr>
                    <td><b>{{ $trimestre }}</b></td>
                    @foreach ($delegaciones as $delegacion)
                        @if($delegacion->nombre != '00 GENERAL GLOBAL')
                            @php
                                $totalTrimestreDelegacion = $totalesDelegacion[$delegacion->id] ?? 0;
                            @endphp
                            <td>{{ number_format($totalTrimestreDelegacion, 2) }} €</td>
                        @endif
                    @endforeach
                    @php
                        $totalTrimestreGeneral = $totalesDelegacion['total_general'] ?? 0;
                    @endphp
                    <td><b>{{ number_format($totalTrimestreGeneral, 2) }} €</b></td>
                </tr>
            @endforeach
            <tr>
                <td><b>Total Anual</b></td>
                @foreach ($delegaciones as $delegacion)
                    @if($delegacion->nombre != '00 GENERAL GLOBAL')
                        @php
                            $totalAnualDelegacionCompras = 0;
                            foreach ($totalesPorDelegacionYTrimestreCompras as $totalesDelegacion) {
                                $totalAnualDelegacionCompras += $totalesDelegacion[$delegacion->id] ?? 0;
                            }
                        @endphp
                        <td><b>{{ number_format($totalAnualDelegacionCompras, 2) }} €</b></td>
                    @endif
                @endforeach
                @php
                    $totalAnualGeneralCompras = 0;
                    foreach ($totalesPorDelegacionYTrimestreCompras as $totalesDelegacion) {
                        $totalAnualGeneralCompras += $totalesDelegacion['total_general'] ?? 0;
                    }
                @endphp
                <td><b>{{ number_format($totalAnualGeneralCompras, 2) }} €</b></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Resultados -->
    <h2>Resultado (A - B) = C</h2>
    <table>
        <thead>
            <tr>
                <th>Resultado</th>
                @foreach ($delegaciones as $delegacion)
                    @if($delegacion->nombre != '00 GENERAL GLOBAL')
                        <th>{{ $delegacion->nombre }}</th>
                    @endif
                @endforeach
                <th>Total General</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>Resultado</b></td>
                @foreach ($delegaciones as $delegacion)
                    @if($delegacion->nombre != '00 GENERAL GLOBAL')
                        <td>{{ number_format($resultadosPorDelegacion[$delegacion->COD] ?? 0, 2) }}€</td>
                    @endif
                @endforeach
                <td><b>{{ number_format(array_sum($resultadosPorDelegacion), 2) }}€</b></td>
            </tr>
        </tbody>
    </table>
</body>
</html>