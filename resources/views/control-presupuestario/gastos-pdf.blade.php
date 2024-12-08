<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gastos</title>
    <style>
        body {
            font-size: 10px;
            margin: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 2px;
            white-space: nowrap;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <h1>Control Presupuestario PTO. GASTOS</h1>
    @foreach($delegaciones as $delegacion)
        <h2>{{ $delegacion->nombre }}</h2>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">Relación Gasto</th>
                    @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                        <th colspan="3">Trimestre {{ $trimestre }}</th>
                    @endfor
                </tr>
                <tr>
                    @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                        @foreach([1, 2, 3] as $mes)
                            <th>{{ \Carbon\Carbon::createFromFormat('!m', ($trimestre - 1) * 3 + $mes)->locale('es')->translatedFormat('F') }}</th>
                        @endforeach
                    @endfor
                </tr>
                <tr>
                    <td><strong>Total Mensual</strong></td>
                    @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                        @foreach([1, 2, 3] as $mes)
                            @php
                                $mesReal = ($trimestre - 1) * 3 + $mes;
                                $totalMesDelegacion = 0;
                                foreach ($proveedores as $proveedor) {
                                    $totalMesDelegacion += $gastosPorTrimestre[$trimestre][$mesReal][$delegacion->nombre][$proveedor] ?? 0;
                                }
                            @endphp
                            <th><strong>{{ number_format($totalMesDelegacion, 2) }}€</strong></th>
                        @endforeach
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($proveedores as $proveedor)
                    @php
                        $mostrarProveedor = false;
                        foreach (range(1, 4) as $trimestre) {
                            foreach ([1, 2, 3] as $mes) {
                                $mesReal = ($trimestre - 1) * 3 + $mes;
                                if (($gastosPorTrimestre[$trimestre][$mesReal][$delegacion->nombre][$proveedor] ?? 0) > 0) {
                                    $mostrarProveedor = true;
                                    break 2; // Salir de ambos bucles si se encuentra un valor > 0
                                }
                            }
                        }
                    @endphp
                    @if($mostrarProveedor)
                        <tr>
                            <td>{{ $proveedor }}</td>
                            @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                                @foreach([1, 2, 3] as $mes)
                                    @php
                                        $mesReal = ($trimestre - 1) * 3 + $mes;
                                        $totalMes = $gastosPorTrimestre[$trimestre][$mesReal][$delegacion->nombre][$proveedor] ?? 0;
                                    @endphp
                                    <td>{{ number_format($totalMes, 2) }}€</td>
                                @endforeach
                            @endfor
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div class="page-break"></div> <!-- Salto de página -->
    @endforeach
</body>
</html> 