@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. Marketing')

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
    <h2>Control PTO. Marketing {{ $year }}</h2>

    <!-- Filtro por año -->
    <form action="{{ route('control-presupuestario.marketing') }}" method="GET" class="mb-4">
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
                        <tr class="mes-header" data-toggle="collapse" data-target="#collapse-{{ $trimestre }}-{{ $mes }}" aria-expanded="false">
                            <td>
                                {{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}
                                <span class="collapse-icon">▼</span>
                            </td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    // Obtener el total de caja para la delegación
                                    $totalCajaDelegacion = $totalesPorDelegacion[$delegacion->nombre] ?? 0;

                                    // Obtener el total de productos vendidos para la delegación en este mes
                                    $totalProductosDelegacion = array_reduce($ventasPorTrimestre[$trimestre][$mes] ?? [], function ($carry, $producto) use ($delegacion) {
                                        return $carry + ($producto['ventasDelegaciones'][$delegacion->nombre]['costeTotal'] ?? 0);
                                    }, 0);

                                    // Sumar el total de caja y productos
                                    $totalDelegacion = $totalCajaDelegacion + $totalProductosDelegacion;

                                    // Sumar al total del mes
                                    $totalMesGeneral += $totalDelegacion;

                                    // Acumular en el total trimestral
                                    $totalesTrimestrePorDelegacion[$delegacion->nombre] += $totalDelegacion;
                                @endphp
                                <td>{{ number_format($totalDelegacion, 2, ',', '.') }}€</td>
                            @endforeach
                            <td>{{ number_format($totalMesGeneral, 2, ',', '.') }}€</td>
                        </tr>

                        <!-- Caja y Productos del mes (tabla colapsada) -->
                        <tr>
                            <td colspan="{{ count($delegaciones) + 2 }}">
                                <div id="collapse-{{ $trimestre }}-{{ $mes }}" class="collapse">
                                    <table class="table table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                @foreach($delegaciones as $delegacion)
                                                    <th>{{ $delegacion->nombre }}</th>
                                                @endforeach
                                                <th>Total Producto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Total de Caja como el primer "producto" -->
                                            <tr>
                                                <td><strong>Facturas</strong></td>
                                                @php
                                                    $totalCaja = 0;
                                                @endphp
                                                @foreach($delegaciones as $delegacion)
                                                    @php
                                                        $cajaDelegacion = $totalesPorDelegacion[$delegacion->nombre] ?? 0;
                                                        $totalCaja += $cajaDelegacion;
                                                    @endphp
                                                    <td>{{ number_format($cajaDelegacion, 2, ',', '.') }}€</td>
                                                @endforeach
                                                <td>{{ number_format($totalCaja, 2, ',', '.') }}€</td>
                                            </tr>

                                            <!-- Listado de Productos -->
                                            @foreach($ventasPorTrimestre[$trimestre][$mes] ?? [] as $producto)
                                                <tr>
                                                    <td>{{ $producto['nombre'] }}</td>
                                                    @php
                                                        $totalProducto = 0;
                                                    @endphp
                                                    @foreach($delegaciones as $delegacion)
                                                        @php
                                                            $productoDelegacion = $producto['ventasDelegaciones'][$delegacion->nombre]['costeTotal'] ?? 0;
                                                            $totalProducto += $productoDelegacion;
                                                        @endphp
                                                        <td>{{ number_format($productoDelegacion, 2, ',', '.') }}€</td>
                                                    @endforeach
                                                    <td>{{ number_format($totalProducto, 2, ',', '.') }}€</td>
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
    <form action="{{ route('control-presupuestario.guardarCostes') }}" method="POST" id="costesForm">
        @csrf
        <input type="hidden" name="año" value="{{ $year }}">

        <!-- Campo oculto para almacenar los IDs de los costes eliminados -->
        <input type="hidden" name="eliminados" id="eliminados" value="">

        <!-- Tabla dinámica para añadir/editar costes -->
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
            <!-- Mostrar los costes existentes -->
            @foreach($costesPorDelegacion as $delegacion => $costes)
                @foreach($costes as $coste)
                    <tr data-id="{{ $coste->id }}">
                        <!-- Campo oculto para el ID del coste -->
                        <input type="hidden" name="coste_ids[]" value="{{ $coste->id }}">
                        
                        <td>
                            <select class="form-control producto-select" name="productos[]" required>
                                <option value="">Seleccione un producto</option>
                                @foreach($productos2 as $producto)
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
                            <!-- Botón para eliminar -->
                            <form action="{{ route('costes.eliminar', $coste->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este coste?');">
                                @csrf
                                {{-- @method('DELETE') --}}
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
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
    
        // Añadir nueva fila a la tabla
        $('#addRowBtn').click(function() {
            var newRow = `
                <tr>
                    <td>
                        <select class="form-control producto-select" name="productos[]" required>
                            <option value="">Seleccione un producto</option>
                            @foreach($productos2 as $producto)
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
    
        // Eliminar fila de la tabla
        $(document).on('click', '.remove-row', function() {
            let row = $(this).closest('tr');
            let costeId = $(this).data('id');
    
            // Si tiene un ID, añadirlo a la lista de eliminados
            if (costeId) {
                eliminados.push(costeId); 
                $('#eliminados').val(eliminados.join(',')); // Actualizar el campo oculto
            }
    
            row.remove(); // Eliminar la fila visualmente
        });


        $('.delete-cost').click(function() {
            const costeId = $(this).data('id');
        
            // Confirmación antes de eliminar
            if (confirm('¿Estás seguro de eliminar este coste?')) {
                $.ajax({
                    url: `/costes/${costeId}`, // Cambia esto a la ruta correcta para tu aplicación
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Incluye el token CSRF
                    },
                    success: function(response) {
                        // Si la eliminación fue exitosa, elimina la fila
                        $(`tr[data-id="${costeId}"]`).remove();
                        alert('Coste eliminado correctamente.');
                    },
                    error: function(xhr) {
                        // Manejo de errores
                        alert('No se pudo eliminar el coste. Por favor, inténtalo de nuevo.');
                    }
                });
            }
        });
    });
    </script>
@endsection
