@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. Patrocinios')

@section('head')
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        table th, table td {
            white-space: nowrap;
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
    </style>
@endsection

@section('content-principal')

<div class="container-fluid">
    <h2>Control PTO. Patrocinios {{ $year }}</h2>

    <!-- Filtro por año -->
    <form action="{{ route('control-presupuestario.patrocinios') }}" method="GET" class="mb-4">
        <div class="form-group">
            <label for="year">Seleccionar Año:</label>
            <select name="year" id="year" class="form-control w-25 d-inline-block">
                @for($i = 2020; $i <= \Carbon\Carbon::now()->year; $i++)
                    <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    @foreach ($cajaPorTrimestre as $trimestre => $cajaPorMes)
    <h3>Trimestre {{ $trimestre }}</h3>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="trimestre-header text-center">
                    <th>Mes</th>
                    @foreach($delegaciones as $delegacion)
                        <th>{{ $delegacion->nombre }}</th>
                    @endforeach
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Ordenar los meses de cada trimestre antes de iterar
                    ksort($cajaPorMes);
                    
                    // Inicializar los totales del trimestre por delegación
                    $totalesTrimestrePorDelegacion = [];
                    foreach ($delegaciones as $delegacion) {
                        $totalesTrimestrePorDelegacion[$delegacion->nombre] = 0;
                    }
                    $totalTrimestreGeneral = 0; // Total general del trimestre
                @endphp

                @foreach ($cajaPorMes as $mes => $totalesPorDelegacion)
                    @php
                        // Inicializar el total del mes
                        $totalMesGeneral = 0;
                    @endphp
                    <tr class="mes-header">
                        <td>
                            {{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}
                        </td>
                        @foreach($delegaciones as $delegacion)
                            @php
                                // Obtener el total de caja para la delegación
                                $totalDelegacion = $totalesPorDelegacion[$delegacion->nombre] ?? 0;

                                // Sumar al total del mes
                                $totalMesGeneral += $totalDelegacion;

                                // Acumular en el total trimestral
                                $totalesTrimestrePorDelegacion[$delegacion->nombre] += $totalDelegacion;
                            @endphp
                            <td>{{ number_format($totalDelegacion, 2, ',', '.') }}€</td>
                        @endforeach
                        <td>{{ number_format($totalMesGeneral, 2, ',', '.') }}€</td>
                    </tr>
                @endforeach

                <!-- Fila de totales del trimestre por delegación -->
                <tr class="trimestre-header">
                    <td>Total Trimestre {{ $trimestre }}</td>
                    @php
                        $totalTrimestreGeneral = 0;
                    @endphp
                    @foreach($delegaciones as $delegacion)
                        @php
                            $totalTrimestreDelegacion = $totalesTrimestrePorDelegacion[$delegacion->nombre];
                            $totalTrimestreGeneral += $totalTrimestreDelegacion;
                        @endphp
                        <td>{{ number_format($totalTrimestreDelegacion, 2, ',', '.') }}€</td>
                    @endforeach
                    <td>{{ number_format($totalTrimestreGeneral, 2, ',', '.') }}€</td>
                </tr>

            </tbody>
        </table>
    </div>
@endforeach
</div>

@endsection
