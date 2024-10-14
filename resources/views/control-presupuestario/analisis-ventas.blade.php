@extends('layouts.app')

@section('title', 'Control Presupuestario Análisis de Ventas')

@section('head')
    <style>
        .table-responsive {
            overflow-x: auto;
            width: 48%; /* Ajusta el tamaño de las tablas */
            display: inline-block;
            vertical-align: top;
        }

        table th, table td {
            white-space: nowrap;
            font-size: 0.8em; /* Reducir el tamaño de la fuente */
            padding: 5px 10px; /* Reducir el relleno */
        }

        .trimestre-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .mes-header {
            font-weight: bold;
            background-color: grey;
            text-align: center;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        /* Estilo para los gráficos */
        .chart-container {
            width: 48%; /* Ajustar para que sea del mismo tamaño que la tabla */
            display: inline-block;
            vertical-align: top;
        }
    </style>
@endsection

@section('content-principal')
<div class="container-fluid mb-5">
    <h2>Análisis de Ventas - Trimestre {{ $trimestre }} - Año {{ $year }}</h2>
    
    <!-- Formulario para seleccionar trimestre y año -->
    <form action="{{ route('control-presupuestario.analisis-ventas') }}" method="GET" class="mb-4">
        <div class="form-group">
            <label for="year">Seleccionar Año:</label>
            <select name="year" id="year" class="form-control w-25 d-inline-block">
                @for($i = 2020; $i <= \Carbon\Carbon::now()->year; $i++)
                    <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>

            <label for="trimestre">Seleccionar Trimestre:</label>
            <select name="trimestre" id="trimestre" class="form-control w-25 d-inline-block">
                @foreach([1, 2, 3, 4] as $trim)
                    <option value="{{ $trim }}" {{ $trim == $trimestre ? 'selected' : '' }}>Trimestre {{ $trim }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <div class="row">
        <!-- Tabla general de ventas por delegación -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover mb-5">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Delegaciones</th>
                        @foreach($meses as $mes)
                            <th>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</th>
                        @endforeach
                        <th>Total</th>
                        <th>% Venta</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delegaciones as $delegacion)
                        <tr>
                            <td>{{ $delegacion->nombre }}</td>
                            @foreach($meses as $mes)
                                <td>{{ number_format($ventasPorDelegacion[$mes][$delegacion->nombre], 2, ',', '.') }} €</td>
                            @endforeach
                            <td>{{ number_format($totalesPorDelegacion[$delegacion->nombre], 2, ',', '.') }} €</td>
                            <td>{{ number_format($porcentajeVentasPorDelegacion[$delegacion->nombre], 2, ',', '.') }} %</td>
                        </tr>
                    @endforeach

                    <!-- Fila para los totales generales -->
                    <tr class="trimestre-header">
                        <td><strong>Total General</strong></td>
                        @foreach($meses as $mes)
                            <td><strong>{{ number_format(array_sum(array_column($ventasPorDelegacion, $mes)), 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format($totalGeneralVentas, 2, ',', '.') }} €</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Sección de gráficos -->
        <div class="chart-container">
            <canvas id="ventasChart"></canvas>
        </div>
    </div>

    <!-- Tablas por producto con gráficos -->
    @foreach($productos as $producto)
    <div class="row">
        <!-- Tabla de ventas por producto -->
        <div class="table-responsive">
            <h3>{{ $producto->nombre }}</h3>
            <table class="table table-bordered table-striped table-hover mb-5">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Delegaciones</th>
                        @foreach($meses as $mes)
                            <th>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delegaciones as $delegacion)
                        <tr>
                            <td>{{ $delegacion->nombre }}</td>
                            @foreach($meses as $mes)
                                <td>{{ $ventasPorProducto[$producto->id][$mes][$delegacion->nombre] }}</td>
                            @endforeach
                            <td>{{ array_sum(array_column($ventasPorProducto[$producto->id], $delegacion->nombre)) }}</td>
                        </tr>
                    @endforeach

                    <!-- Fila para los totales generales por producto -->
                    <tr class="trimestre-header">
                        <td><strong>Total General</strong></td>
                        @foreach($meses as $mes)
                            <td><strong>{{ array_sum(array_column($ventasPorProducto[$producto->id], $mes)) }}</strong></td>
                        @endforeach
                        <td><strong>{{ array_sum($totalesPorProducto[$producto->id]) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Gráfico de ventas por producto -->
        <div class="chart-container">
            <canvas id="chart-{{ $producto->id }}"></canvas>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para el gráfico de ventas generales por delegación
    var delegaciones = @json($delegaciones->pluck('nombre'));
    var ventasTotales = @json(array_values($totalesPorDelegacion));

    // Configuración del gráfico de barras
    var ctx = document.getElementById('ventasChart').getContext('2d');
    var ventasChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: delegaciones,
            datasets: [{
                label: 'Ventas Totales (€)',
                data: ventasTotales,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, values) {
                            return '€' + value.toLocaleString(); // Formato de euros en el eje y
                        }
                    }
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    display: false // Ocultar leyenda
                }
            }
        }
    });

    // Generar gráficos para cada producto
    @foreach($productos as $producto)
    var ctx{{ $producto->id }} = document.getElementById('chart-{{ $producto->id }}').getContext('2d');
    var chart{{ $producto->id }} = new Chart(ctx{{ $producto->id }}, {
        type: 'bar',
        data: {
            labels: delegaciones,
            datasets: [{
                label: '{{ $producto->nombre }} - Ventas por delegación',
                data: @json(array_values($totalesPorProducto[$producto->id])),
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' unidades'; // Formato para las unidades
                        }
                    }
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
    @endforeach
</script>
@endsection
