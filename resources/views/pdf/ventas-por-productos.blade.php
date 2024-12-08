<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ventas por Productos</title>
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
    <h2 class="text-center">Ventas por Productos - Año {{ $year }}</h2>
    @foreach ($delegaciones as $delegacion)
        <h3>{{ $delegacion['nombre'] }}</h3>
        <table class="table">
            <thead>
                <tr>
                    <th rowspan="2">Trimestre</th>
                    @php
                        $productosUnicos = [];
                        if (isset($ventasPorTrimestre[$delegacion['nombre']])) {
                            foreach ($ventasPorTrimestre[$delegacion['nombre']] as $productos) {
                                foreach ($productos as $productoNombre => $detalle) {
                                    if (!in_array($productoNombre, $productosUnicos)) {
                                        $productosUnicos[] = $productoNombre;
                                    }
                                }
                            }
                        }
                    @endphp
                    @foreach ($productosUnicos as $producto)
                        <th colspan="2">{{ $producto }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($productosUnicos as $producto)
                        <th>Con Cargo</th>
                        <th>Sin Cargo</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @if (isset($ventasPorTrimestre[$delegacion['nombre']]))
                    @foreach ($ventasPorTrimestre[$delegacion['nombre']] as $trimestre => $productos)
                        <tr>
                            <td>{{ $trimestre }} TRIMESTRE</td>
                            @foreach ($productosUnicos as $producto)
                                <td>{{ $productos[$producto]['conCargo'] ?? 0 }}</td>
                                <td>{{ $productos[$producto]['sinCargo'] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="{{ count($productosUnicos) * 2 + 1 }}">Sin datos</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endforeach
</body>
</html> 