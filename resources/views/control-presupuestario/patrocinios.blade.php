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
    <button onclick="exportarTablasAExcel()" class="btn btn-success mb-4">Exportar a Excel</button>

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
                        <td style="text-transform: uppercase;">
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
        XLSX.writeFile(wb, 'control_presupuestario_logistica.xlsx');
    }
</script>
@endsection
