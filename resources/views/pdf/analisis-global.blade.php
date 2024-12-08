<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Análisis Global</title>
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
    <h2 class="text-center">Análisis Global - Trimestre {{ $trimestre }} - Año {{ $year }}</h2>

    <!-- Tabla de ventas por trimestre y delegación -->
    <h3>(A) Ventas por Trimestre</h3>
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
            @foreach($ventasPorDelegacion as $mes => $delegacionVentas)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($delegacionVentas[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($delegacionVentas), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de compras por trimestre y delegación -->
    <h3>(B) Compras por Trimestre</h3>
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
            @foreach($comprasPorDelegacion as $mes => $comprasMes)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($comprasMes[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($comprasMes), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Resultados (A-B) -->
    <h3>Resultado (A-B) = C por Trimestre</h3>
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
            @foreach($resultadosPorDelegacion as $mes => $resultadosMes)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($resultadosMes[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($resultadosMes), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de gastos estructurales (D) -->
    <h3>(D) Gasto Estructural por Trimestre</h3>
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
            @foreach($gastosEstructuralesPorDelegacion as $mes => $gastosMes)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($gastosMes[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($gastosMes), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de gastos variables (E) -->
    <h3>(E) Gasto Variable por Trimestre</h3>
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
            @foreach($gastosVariablesPorDelegacion as $mes => $gastosMes)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($gastosMes[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($gastosMes), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de gastos logísticos (G) -->
    <h3>(G) Gasto Logístico por Trimestre</h3>
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
            @foreach($gastosLogisticaPorDelegacion as $mes => $gastosMes)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($gastosMes[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($gastosMes), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Resultado (C-D-E-F) -->
    <h3>Resultado (C-D-E-F) = G por Trimestre</h3>
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
            @foreach($resultadosPorDelegacion as $mes => $resultadosMes)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        @php
                            $resultadoC = $resultadosMes[$delegacion->nombre] ?? 0;
                            $gastoEstructuralD = $gastosEstructuralesPorDelegacion[$mes][$delegacion->nombre] ?? 0;
                            $gastoVariableE = $gastosVariablesPorDelegacion[$mes][$delegacion->nombre] ?? 0;
                            $gastoLogisticoF = $gastosLogisticaPorDelegacion[$mes][$delegacion->nombre] ?? 0;
                            $resultadoGDelegacionMes = $resultadoC - $gastoEstructuralD - $gastoVariableE - $gastoLogisticoF;
                        @endphp
                        <td>{{ number_format($resultadoGDelegacionMes, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($resultadosMes), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Inversión Comercial (IC) -->
    <h3>(IC) Inversión Comercial por Trimestre</h3>
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
            @foreach($inversionComercialPorDelegacion as $mes => $delegacionesInversion)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($delegacionesInversion[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($delegacionesInversion), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Inversión Marketing (IMKT) -->
    <h3>(IMKT) Inversión MKT por Trimestre</h3>
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
            @foreach($inversionMarketingPorDelegacion as $mes => $delegacionesInversion)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($delegacionesInversion[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($delegacionesInversion), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Tabla de Inversión Patrocinio (IP) -->
    <h3>(IP) Inversión Patrocinio por Trimestre</h3>
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
            @foreach($inversionPatrocinioPorDelegacion as $mes => $delegacionesInversion)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($delegacionesInversion[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($delegacionesInversion), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Resultado (G-I) -->
    <h3>Resultado (G-I) por Trimestre</h3>
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
            @foreach($resultadoPorDelegacionGI as $mes => $delegacionesResultado)
                <tr class="mes-header">
                    <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                    @foreach($delegaciones as $delegacion)
                        <td>{{ number_format($delegacionesResultado[$delegacion->nombre] ?? 0, 2, ',', '.') }} €</td>
                    @endforeach
                    <td>{{ number_format(array_sum($delegacionesResultado), 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 