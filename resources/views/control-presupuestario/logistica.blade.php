@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. LOGÍSTICA')

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
    <h2>Gastos de Transporte por Logística - Año {{ $year }}</h2>

    <!-- Filtro por año -->
    <form action="{{ route('control-presupuestario.logistica') }}" method="GET" class="mb-4">
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

    <!-- Iterar sobre los trimestres -->
    @foreach ($gastosTransportePorTrimestre as $trimestre => $gastosPorMes)
        <h3>Trimestre {{ $trimestre }}</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th style="text-transform: uppercase;"> {{ $delegacion->nombre }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <!-- Mostrar los meses del trimestre -->
                    @foreach ($gastosPorMes as $mes => $gastosPorDelegacion)
                        <tr class="mes-header">
                            <td style="text-transform: uppercase;">
                                {{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}
                            </td>

                            <!-- Mostrar los gastos de cada delegación -->
                            @foreach($delegaciones as $delegacion)
                                <td class="text-center">
                                    {{ number_format($gastosPorDelegacion[$delegacion->nombre] ?? 0, 2, ',', '.') }}€
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    <!-- Fila con el total del trimestre -->
                    <tr class="trimestre-header">
                        <td>Total del Trimestre</td>
                        @foreach($delegaciones as $delegacion)
                            <td class="text-center">
                                {{ number_format($totalPorTrimestre[$trimestre][$delegacion->nombre] ?? 0, 2, ',', '.') }}€
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</div>

@endsection
