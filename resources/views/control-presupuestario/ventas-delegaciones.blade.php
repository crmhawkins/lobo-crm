@extends('layouts.app')

@section('title', 'Control Presupuestario Ventas Delegaciones')

@section('head')
    @vite(['resources/sass/productos.scss', 'resources/sass/alumnos.scss'])
    <style>
        ul.pagination {
            justify-content: center;
        }

        /* Evita que las celdas se dividan en varias líneas */
        table th, table td {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content-principal')

    <div class="container" style="min-height: 100vh;">
        <h2 class="text-center mb-4 d-flex justify-content-center gap-2 align-items-center">Presupuesto por Ventas delegaciones {{ $year }}</h2>
        <table class="table table-bordered table-striped table-hover mb-5">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="2">Delegaciones</th>
                    <th colspan="2">1 TRIMESTRE</th>
                    <th colspan="2">2 TRIMESTRE</th>
                    <th colspan="2">3 TRIMESTRE</th>
                    <th colspan="2">4 TRIMESTRE</th>
                    <th rowspan="2">Total</th> <!-- Columna de total -->
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
                @php
                    $totalAnual = $totalVentas['anual'];
                @endphp

                @foreach ($delegaciones as $delegacion)
                @php
                    $ventas = $delegacionVentas[$delegacion->nombre] ?? ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0];
                    $totalDelegacion = $ventas['1T'] + $ventas['2T'] + $ventas['3T'] + $ventas['4T']; // Total de ventas por delegación
                @endphp
                <tr>
                    <td>{{ $delegacion->nombre }}</td>
        
                    <!-- 1er Trimestre -->
                    <td>{{ number_format($ventas['1T'], 2, ',', '.') }} €</td>
                    <td>
                        @if ($totalVentas['1T'] > 0)
                            {{ number_format(($ventas['1T'] / $totalVentas['1T']) * 100, 2, ',', '.') }} %
                        @else
                            0.00 %
                        @endif
                    </td>
        
                    <!-- 2do Trimestre -->
                    <td>{{ number_format($ventas['2T'], 2, ',', '.') }} €</td>
                    <td>
                        @if ($totalVentas['2T'] > 0)
                            {{ number_format(($ventas['2T'] / $totalVentas['2T']) * 100, 2, ',', '.') }} %
                        @else
                            0.00 %
                        @endif
                    </td>
        
                    <!-- 3er Trimestre -->
                    <td>{{ number_format($ventas['3T'], 2, ',', '.') }} €</td>
                    <td>
                        @if ($totalVentas['3T'] > 0)
                            {{ number_format(($ventas['3T'] / $totalVentas['3T']) * 100, 2, ',', '.') }} %
                        @else
                            0.00 %
                        @endif
                    </td>
        
                    <!-- 4to Trimestre -->
                    <td>{{ number_format($ventas['4T'], 2, ',', '.') }} €</td>
                    <td>
                        @if ($totalVentas['4T'] > 0)
                            {{ number_format(($ventas['4T'] / $totalVentas['4T']) * 100, 2, ',', '.') }} %
                        @else
                            0.00 %
                        @endif
                    </td>

                    <!-- Total de cada delegación -->
                    <td>{{ number_format($totalDelegacion, 2, ',', '.') }} €</td> <!-- Muestra el total por delegación -->
                </tr>
                @endforeach

                <!-- Totales generales -->
                <tr class="font-weight-bold">
                    <td>Total</td>
                    <td>{{ number_format($totalVentas['1T'], 2, ',', '.') }} €</td>
                    <td>100.00 %</td>
                    <td>{{ number_format($totalVentas['2T'], 2, ',', '.') }} €</td>
                    <td>100.00 %</td>
                    <td>{{ number_format($totalVentas['3T'], 2, ',', '.') }} €</td>
                    <td>100.00 %</td>
                    <td>{{ number_format($totalVentas['4T'], 2, ',', '.') }} €</td>
                    <td>100.00 %</td>

                    <!-- Total general anual -->
                    <td>{{ number_format($totalAnual, 2, ',', '.') }} €</td> <!-- Total general al final -->
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered table-striped table-hover mb-5">
            <thead class="thead-dark">
                <tr>
                    <th >Delegaciones</th>
                    <th >1 TRIMESTRE</th>
                    <th >2 TRIMESTRE</th>
                    <th >3 TRIMESTRE</th>
                    <th >4 TRIMESTRE</th>
                    <th >% Gastos Estructurales</th> <!-- Columna de total -->
                </tr>
            </thead>
            @php
                $total1T = 0;
                $total2T = 0;
                $total3T = 0;
                $total4T = 0;
                $totalEstructurales = 0;
            @endphp
            <tbody>
                @foreach ($delegaciones as $delegacion)
                    @php
                    $ventas = $delegacionVentas[$delegacion->nombre] ?? ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0];
                    $totalDelegacion = $ventas['1T'] + $ventas['2T'] + $ventas['3T'] + $ventas['4T']; // Total de ventas por delegación
                    $porcentajeT1 =  $totalVentas['1T'] > 0 ? ($ventas['1T'] / $totalVentas['1T']) * 100 : 0;
                    $porcentajeT2 =  $totalVentas['2T'] > 0 ? ($ventas['2T'] / $totalVentas['2T']) * 100 : 0;
                    $porcentajeT3 =  $totalVentas['3T'] > 0 ? ($ventas['3T'] / $totalVentas['3T']) * 100 : 0;
                    $porcentajeT4 =  $totalVentas['4T'] > 0 ? ($ventas['4T'] / $totalVentas['4T']) * 100 : 0;

                    $totalGastosEstructurales= ($porcentajeT1 + $porcentajeT2 + $porcentajeT3 + $porcentajeT4) / 4;

                    $total1T += $porcentajeT1;
                    $total2T += $porcentajeT2;
                    $total3T += $porcentajeT3;
                    $total4T += $porcentajeT4;
                    $totalEstructurales += $totalGastosEstructurales;

                    @endphp

                    <tr>
                        <td>{{ $delegacion->nombre }}</td>

                        <td>{{ number_format($porcentajeT1, 2, ',', '.') }} %</td>
                        <td>{{ number_format($porcentajeT2, 2, ',', '.') }} %</td>
                        <td>{{ number_format($porcentajeT3, 2, ',', '.') }} %</td>
                        <td>{{ number_format($porcentajeT4, 2, ',', '.') }} %</td>
                        <td>{{ number_format($totalGastosEstructurales, 2, ',', '.') }} %</td>
                        
                    </tr>
                @endforeach
                <tr>
                    <td>Total</td>
                    <td>{{ number_format($total1T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($total2T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($total3T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($total4T, 2, ',', '.') }} %</td>
                    <td>{{ number_format($totalEstructurales, 2, ',', '.') }} %</td>
                </tr>
            </tbody>
        </table>
    </div>

@endsection
