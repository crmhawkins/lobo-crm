@extends('layouts.app')

@section('title', 'Control Presupuestario Ventas Delegaciones')

@section('head')
    @vite(['resources/sass/productos.scss', 'resources/sass/alumnos.scss'])
    <style>
        ul.pagination {
            justify-content: center;
        }

        /* Scroll horizontal en las tablas */
        .table-responsive {
            overflow-x: auto;
        }

        /* Evita que las celdas se dividan en varias líneas */
        table th, table td {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content-principal')
    <div class="container mb-5">
        <h2 class="text-center mb-4">Ventas por Productos - Año {{ $year }}</h2>

        @foreach ($delegaciones as $delegacion)
            <h3>{{ $delegacion['nombre'] }}</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">Trimestre</th>
                            @php
                                // Obtener todos los productos únicos para las columnas
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

                            <!-- Fila de nombres de productos que ocupan 2 columnas cada uno -->
                            @foreach ($productosUnicos as $producto)
                                <th colspan="2" class="text-center">{{ $producto }}</th>
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
                                <td colspan="{{ count($productosUnicos) * 2 + 1 }}" class="text-center">Sin datos</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
@endsection
