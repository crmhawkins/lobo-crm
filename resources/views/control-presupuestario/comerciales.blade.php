@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. COMERCIALES')

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
            cursor: pointer;
        }

        .collapse-icon {
            margin-left: 10px;
            transition: transform 0.2s;
        }

        .collapsed .collapse-icon {
            transform: rotate(90deg);
        }
    </style>
@endsection

@section('content-principal')

<div class="container-fluid">
    <h2>Control PTO. COMERCIALES {{ $year }}</h2>

    <!-- Filtro por año -->
    <form action="{{ route('control-presupuestario.comerciales') }}" method="GET" class="mb-4">
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
    @foreach ($ventasPorTrimestre as $trimestre => $ventasPorMes)
        <h3>Trimestre {{ $trimestre }}</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        <!-- Crear un th para cada delegación -->
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th> <!-- Nueva columna para el total de la fila -->
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Inicializar un array para acumular los totales del trimestre por delegación
                        $totalesTrimestrePorDelegacion = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesTrimestrePorDelegacion[$delegacion->nombre] = 0;
                        }
                    @endphp

                    <!-- Iterar sobre los meses dentro del trimestre -->
                    @foreach ($ventasPorMes as $mes => $productos)
                        @php
                            $totalesPorDelegacion = [];
                            foreach ($delegaciones as $delegacion) {
                                $totalesPorDelegacion[$delegacion->nombre] = 0;
                            }
                            $totalGeneral = 0;
                        @endphp

                        <!-- Fila para el mes -->
                        <tr class="mes-header" data-toggle="collapse" data-target="#collapse-{{ $mes }}" aria-expanded="false">
                            <td class="mes-header">
                                {{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}
                                <span class="collapse-icon">▼</span>
                            </td>

                            <!-- Mostrar los totales por delegación -->
                            @foreach($delegaciones as $delegacion)
                                @php
                                    // Asegurarse de buscar correctamente en 'ventasDelegaciones'
                                    $totalVentasDelegacion = array_reduce($productos, function ($carry, $producto) use ($delegacion) {
                                        return $carry + ($producto['ventasDelegaciones'][$delegacion->nombre]['costeTotal'] ?? 0);
                                    }, 0);
                                    $totalesPorDelegacion[$delegacion->nombre] += $totalVentasDelegacion;
                                    $totalGeneral += $totalVentasDelegacion;

                                    // Sumar al total trimestral
                                    $totalesTrimestrePorDelegacion[$delegacion->nombre] += $totalVentasDelegacion;
                                @endphp
                                <td>{{ number_format($totalesPorDelegacion[$delegacion->nombre], 2, ',', '.') }}€</td>
                            @endforeach
                            <td>{{ number_format($totalGeneral, 2, ',', '.') }}€</td> <!-- Mostrar el total por fila -->
                        </tr>

                        <!-- Productos del mes (tabla colapsada) -->
                        <tr>
                            <td colspan="{{ count($delegaciones) + 2 }}"> <!-- +2 por la nueva columna Total -->
                                <div id="collapse-{{ $mes }}" class="collapse">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <th>Producto</th>
                                                @foreach($delegaciones as $delegacion)
                                                    <th>{{ $delegacion->nombre }}</th>
                                                @endforeach
                                                <th>Total</th> <!-- Nueva columna Total -->
                                            </tr>
                                            @foreach ($productos as $producto)
                                                @php
                                                    $totalProducto = 0; // Inicializar el total del producto
                                                @endphp
                                                <tr>
                                                    <td>{{ $producto['nombre'] }}</td>
                                                    @foreach($delegaciones as $delegacion)
                                                        @php
                                                            $costeTotalDelegacion = $producto['ventasDelegaciones'][$delegacion->nombre]['costeTotal'] ?? 0;
                                                            $totalProducto += $costeTotalDelegacion;
                                                        @endphp
                                                        <td class="text-center">
                                                            {{ number_format($costeTotalDelegacion, 2, ',', '.') }}€
                                                        </td>
                                                    @endforeach
                                                    <td class="text-center">{{ number_format($totalProducto, 2, ',', '.') }}€</td> <!-- Total del producto por fila -->
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    <!-- Fila de totales del trimestre por delegación -->
                    <tr class="trimestre-header">
                        <td>Total Trimestre {{ $trimestre }}</td>
                        @foreach($delegaciones as $delegacion)
                            <td>{{ number_format($totalesTrimestrePorDelegacion[$delegacion->nombre], 2, ',', '.') }}€</td>
                        @endforeach
                        <td>{{ number_format(array_sum($totalesTrimestrePorDelegacion), 2, ',', '.') }}€</td> <!-- Total del trimestre -->
                    </tr>

                </tbody>
            </table>
        </div>
    @endforeach

    <!-- Formulario para añadir/editar costes -->
    <form action="{{ route('control-presupuestario.guardarCostes') }}" method="POST" id="costesForm">
        @csrf
        <input type="hidden" name="año" value="{{ $year }}">

        <!-- Tabla dinámica para añadir/editar costes -->
        <div class="table-responsive">
            <table class="table table-bordered mb-5" id="costesTable">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Coste</th>
                        <th>Delegación (opcional)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($costesPorDelegacion as $delegacion => $costes)
                        @foreach($costes as $coste)
                            <tr data-id="{{ $coste->id }}">
                                <input type="hidden" name="coste_ids[]" value="{{ $coste->id }}">
                                
                                <td>
                                    <select class="form-control producto-select" name="productos[]" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach($productosGratis as $producto)
                                            <option value="{{ $producto->id }}" {{ $producto->id == $coste->product_id ? 'selected' : '' }}>
                                                {{ $producto->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="costes[]" class="form-control" value="{{ $coste->cost }}" required>
                                </td>
                                <td>
                                    <select class="form-control" name="delegaciones[]">
                                        <option value="" {{ is_null($coste->COD) ? 'selected' : '' }}>General</option>
                                        @foreach($delegaciones as $deleg)
                                            <option value="{{ $deleg->COD }}" {{ (!is_null($coste->COD) && ($coste->COD == $deleg->COD)) ? 'selected' : '' }}>
                                                {{ $deleg->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger remove-row">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-secondary mb-5" id="addRowBtn">Añadir Producto</button>
        <button type="submit" class="btn btn-primary mb-5">Guardar costes</button>
    </form>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let eliminados = $('#eliminados').val() ? $('#eliminados').val().split(',') : [];
    
        $('#addRowBtn').click(function() {
            var newRow = `
                <tr>
                    <td>
                        <select class="form-control producto-select" name="productos[]" required>
                            <option value="">Seleccione un producto</option>
                            @foreach($productosGratis as $producto)
                                <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="costes[]" class="form-control" required>
                    </td>
                    <td>
                        <select class="form-control" name="delegaciones[]">
                            <option value="">General</option>
                            @foreach($delegaciones as $delegacion)
                                <option value="{{ $delegacion->COD }}">{{ $delegacion->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row">Eliminar</button>
                    </td>
                </tr>
            `;
            $('#costesTable tbody').append(newRow);
        });
    
        $(document).on('click', '.remove-row', function() {
            let row = $(this).closest('tr');
            let costeId = $(this).data('id');
    
            if (costeId) {
                eliminados.push(costeId); 
                $('#eliminados').val(eliminados.join(','));
            }
    
            row.remove();
        });
    });
</script>
@endsection
