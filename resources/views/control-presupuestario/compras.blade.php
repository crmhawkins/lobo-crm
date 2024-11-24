@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. COMPRAS')

@section('head')
    <style>
        /* Añadir scroll horizontal a las tablas */
        .table-responsive {
            overflow-x: auto;
        }

        /* Evita que las celdas se dividan en varias líneas */
        table th, table td {
            white-space: nowrap;
        }

        /* Formato de los títulos */
        .trimestre-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Alineación centrada */
        .text-center {
            text-align: center;
        }

        /* Alineación centrada en la cabecera de trimestre */
        .mes-header {
            font-weight: bold;
            background-color: grey;
            text-align: center;
            cursor: pointer;
        }

        /* Estilos para botón desplegable */
        .collapse-icon {
            margin-left: 10px;
            transition: transform 0.2s;
        }

        .collapsed .collapse-icon {
            transform: rotate(90deg); /* Cambiar la dirección de la flecha al colapsar */
        }
    </style>
@endsection

@section('content-principal')

<div class="container-fluid">
    <h2>Control PTO. COMPRAS {{ $year }}</h2>
    <button onclick="exportarTablasAExcel()" class="btn btn-success mb-4">Exportar a Excel</button>

    <!-- Filtro por año -->
    <form action="{{ route('control-presupuestario.compras') }}" method="GET" class="mb-4">
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
                    </tr>
                </thead>
                <tbody>
                    <!-- Iterar sobre los meses dentro del trimestre -->
                    @foreach ($ventasPorMes as $mes => $productos)
                        @php
                            // Inicializar el array para sumar los totales por delegación
                            $totalesPorDelegacion = [];
                            foreach ($delegaciones as $delegacion) {
                                $totalesPorDelegacion[$delegacion->nombre] = 0;
                            }
                            $totalGeneral = 0; // Inicializar el total general para el mes
                            $firstRow = true;
                        @endphp

                        <!-- Fila que actúa como botón para desplegar la tabla -->
                        <tr class="mes-header" data-toggle="collapse" data-target="#collapse-{{ $mes }}" aria-expanded="false">
                            <td class="mes-header">
                                {{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}
                                <span class="collapse-icon">▼</span>
                            </td>

                            <!-- Mostrar los totales por delegación -->
                            @foreach($delegaciones as $delegacion)
                                @php
                                    // Sumar ventas de cada delegación para ese mes
                                    $totalVentasDelegacion = array_reduce($productos, function ($carry, $producto) use ($delegacion) {
                                        return $carry + ($producto['ventasDelegaciones'][$delegacion->nombre]['costeTotal'] ?? 0);
                                    }, 0);
                                    $totalesPorDelegacion[$delegacion->nombre] += $totalVentasDelegacion;
                                    $totalGeneral += $totalVentasDelegacion;
                                @endphp
                                <td>{{ number_format($totalesPorDelegacion[$delegacion->nombre], 2, ',', '.') }}€</td>
                            @endforeach
                        </tr>

                        <!-- Productos del mes (tabla colapsada) -->
                        <tr>
                            <td colspan="{{ count($delegaciones) + 2 }}">
                                <div id="collapse-{{ $mes }}" class="collapse">
                                    <table class="table table-bordered mb-0">
                                        <tbody>
                                            <tr>
                                                <th>Producto</th>
                                                @foreach($delegaciones as $delegacion)
                                                    <th>{{ $delegacion->nombre }}</th>
                                                @endforeach
                                            </tr>
                                            @foreach ($productos as $producto)
                                                <tr>
                                                    <td>{{ $producto['nombre'] }}</td>
                                                    <!-- Mostrar las ventas por delegación para cada producto -->
                                                    @foreach($delegaciones as $delegacion)
                                                        <td class="text-center">
                                                            {{ number_format($producto['ventasDelegaciones'][$delegacion->nombre]['costeTotal'] ?? 0, 2, ',', '.') }}€
                                                        </td>
                                                    @endforeach
                                                    
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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
       <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
       <script>
        function exportarTablasAExcel() {
            // Crear un nuevo libro de trabajo
            var wb = XLSX.utils.book_new();
    
            // Seleccionar todas las tablas dentro del contenedor principal
            document.querySelectorAll('.container-fluid .table-responsive').forEach((tableContainer, index) => {
                // Obtener el nombre de la tabla desde el h3 anterior
                var tableNameElement = tableContainer.previousElementSibling;
                while (tableNameElement && tableNameElement.tagName !== 'H3') {
                    tableNameElement = tableNameElement.previousElementSibling;
                }
                var tableName = tableNameElement ? tableNameElement.textContent.trim() : 'Tabla ' + (index + 1);
    
                // Limpiar el nombre de la hoja eliminando caracteres no permitidos
                tableName = tableName.replace(/[:\\\/?*\[\]]/g, '');
    
                // Truncar el nombre de la hoja si es necesario
                if (tableName.length > 31) {
                    tableName = tableName.substring(0, 28) + '...';
                }
    
                // Convertir la tabla HTML a una hoja de cálculo
                var table = tableContainer.querySelector('table');
                var ws = XLSX.utils.table_to_sheet(table);
    
                // Añadir la hoja de cálculo al libro de trabajo
                XLSX.utils.book_append_sheet(wb, ws, tableName);
            });
    
            // Exportar el libro de trabajo a un archivo Excel
            XLSX.writeFile(wb, 'control_presupuestario_compras.xlsx');
        }
    </script>
@endsection
