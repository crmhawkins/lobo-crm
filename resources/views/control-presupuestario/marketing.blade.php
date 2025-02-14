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

        /* Estilos para el loader */
        #loader {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
@endsection

@section('content-principal')

<div class="container-fluid">
    <h2>Control PTO. Marketing {{ $year }}</h2>
    <button onclick="exportarTablasAExcel()" class="btn btn-success mb-4">Exportar a Excel</button>
    <a href="{{ route('exportarMarketingAPDF', request()->query()) }}" class="btn btn-success mb-4">Exportar a PDF</a>

    <!-- Loader -->
    <div id="loader">
        <p>Generando PDF, por favor espera...</p>
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

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
    <!-- Formulario para costes de productos normales -->
    {{-- <form action="{{ route('control-presupuestario.guardarCostes') }}" method="POST" id="costesFormProductos">
        @csrf
        <input type="hidden" name="año" value="{{ $year }}">

        <h3>Costes de Productos</h3>
        <div class="table-responsive">
            <table class="table table-bordered mb-5" id="costesTableProductos">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Coste</th>
                        <th>Delegación (opcional)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($costesProductosPorDelegacion as $delegacion => $costes)
                        @foreach($costes as $coste)
                            <tr data-id="{{ $coste->id }}">
                                <input type="hidden" name="coste_ids_productos[]" value="{{ $coste->id }}">
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
                                    <form action="{{ route('costes.eliminar', $coste->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este coste?');">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-secondary mb-5" id="addRowBtnProductos">Añadir Producto</button>
        <button type="submit" class="btn btn-primary mb-5">Guardar costes</button>
    </form> --}}

    <!-- Formulario para costes de productos de marketing -->
    {{-- <form action="{{ route('control-presupuestario.guardarCostesMarketing') }}" method="POST" id="costesFormMarketing">
        @csrf
        <input type="hidden" name="año" value="{{ $year }}">

        <h3>Costes de Productos de Marketing</h3>
        <div class="table-responsive">
            <table class="table table-bordered mb-5" id="costesTableMarketing">
                <thead>
                    <tr>
                        <th>Producto Marketing</th>
                        <th>Coste</th>
                        <th>Delegación (opcional)</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($costesMarketingPorDelegacion as $delegacion => $costes)
                        @foreach($costes as $coste)
                            <tr data-id="{{ $coste->id }}">
                                <input type="hidden" name="coste_ids_marketing[]" value="{{ $coste->id }}">
                                <td>
                                    <select class="form-control producto-select" name="productos_marketing[]" required>
                                        <option value="">Seleccione un producto</option>
                                        @foreach($productosMarketing as $productoMarketing)
                                            <option value="{{ $productoMarketing->id }}" {{ $productoMarketing->id == $coste->product_id ? 'selected' : '' }}>
                                                {{ $productoMarketing->nombre }} (Producto marketing)
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="costes_marketing[]" class="form-control" value="{{ $coste->cost }}" required>
                                </td>
                                <td>
                                    <select class="form-control" name="delegaciones_marketing[]">
                                        <option value="" {{ is_null($coste->COD) ? 'selected' : '' }}>General</option>
                                        @foreach($delegaciones as $deleg)
                                            <option value="{{ $deleg->COD }}" {{ (!is_null($coste->COD) && ($coste->COD == $deleg->COD)) ? 'selected' : '' }}>
                                                {{ $deleg->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                        <button  class="btn btn-danger delete-cost-marketing" data-id="{{ $coste->id }}">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-secondary mb-5" id="addRowBtnMarketing">Añadir Producto Marketing</button>
        <button type="submit" class="btn btn-primary mb-5">Guardar costes de marketing</button>
    </form> --}}
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
       <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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
            XLSX.writeFile(wb, 'control_presupuestario_marketing.xlsx');
        }
    </script>
    <script>
        async function exportarTablasAPDF() {
        // Mostrar el loader
        document.getElementById('loader').style.display = 'block';

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        // Seleccionar todas las tablas dentro del contenedor principal
        const tables = document.querySelectorAll('.container-fluid .table');
        for (let index = 0; index < tables.length; index++) {
            const table = tables[index];

            // Obtener el nombre de la tabla desde el h3 anterior
            let tableNameElement = table.closest('.table-responsive').previousElementSibling;
            while (tableNameElement && tableNameElement.tagName !== 'H3') {
                tableNameElement = tableNameElement.previousElementSibling;
            }
            const tableName = tableNameElement ? tableNameElement.textContent.trim() : 'Tabla ' + (index + 1);

            // Convertir la tabla a imagen usando html2canvas
            const canvas = await html2canvas(table);
            const imgData = canvas.toDataURL('image/png');

            // Obtener las propiedades de la imagen
            const imgProps = pdf.getImageProperties(imgData);
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

            // Ajustar la imagen al tamaño de la página
            const pageHeight = pdf.internal.pageSize.getHeight();
            let position = 0;

            // Si la imagen es más alta que la página, dividirla en varias páginas
            if (pdfHeight > pageHeight) {
                const totalPages = Math.ceil(pdfHeight / pageHeight);
                for (let i = 0; i < totalPages; i++) {
                    const sourceY = i * pageHeight;
                    const newCanvas = document.createElement('canvas');
                    newCanvas.width = canvas.width;
                    newCanvas.height = pageHeight * (canvas.width / pdfWidth);
                    const newCtx = newCanvas.getContext('2d');
                    newCtx.drawImage(canvas, 0, sourceY, canvas.width, pageHeight * (canvas.width / pdfWidth), 0, 0, canvas.width, pageHeight * (canvas.width / pdfWidth));
                    const newImgData = newCanvas.toDataURL('image/png');
                    pdf.addImage(newImgData, 'PNG', 0, position, pdfWidth, pageHeight);
                    if (i < totalPages - 1) {
                        pdf.addPage();
                    }
                }
            } else {
                pdf.addImage(imgData, 'PNG', 0, position, pdfWidth, pdfHeight);
            }

            // Añadir una nueva página si no es la última tabla
            if (index < tables.length - 1) {
                pdf.addPage();
            }
        }

        // Guardar el archivo PDF
        pdf.save('marketing.pdf');

        // Ocultar el loader
        document.getElementById('loader').style.display = 'none';
    }
    </script>
{{-- <script>
    $(document).ready(function() {
        let eliminados = $('#eliminados').val() ? $('#eliminados').val().split(',') : [];
    
        // Añadir nueva fila a la tabla de productos normales
        $('#addRowBtnProductos').click(function() {
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
            $('#costesTableProductos tbody').append(newRow);
        });

        // Añadir nueva fila a la tabla de productos de marketing
        $('#addRowBtnMarketing').click(function() {
            var newRow = `
                <tr>
                    <td>
                        <select class="form-control producto-select" name="productos_marketing[]" required>
                            <option value="">Seleccione un producto</option>
                            @foreach($productosMarketing as $productoMarketing)
                                <option value="{{ $productoMarketing->id }}">{{ $productoMarketing->nombre }} (Producto marketing)</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="costes_marketing[]" class="form-control" required>
                    </td>
                    <td>
                        <select class="form-control" name="delegaciones_marketing[]">
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
            $('#costesTableMarketing tbody').append(newRow);
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

        // Código para eliminar costes
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
<script>
    $(document).ready(function() {
        let eliminadosMarketing = $('#eliminadosMarketing').val() ? $('#eliminadosMarketing').val().split(',') : [];
    
        // Añadir nueva fila a la tabla de productos de marketing
        $('#addRowBtnMarketing').click(function() {
            var newRow = `
                <tr>
                    <td>
                        <select class="form-control producto-select" name="productos_marketing[]" required>
                            <option value="">Seleccione un producto</option>
                            @foreach($productosMarketing as $productoMarketing)
                                <option value="{{ $productoMarketing->id }}">{{ $productoMarketing->nombre }} (Producto marketing)</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="costes_marketing[]" class="form-control" required>
                    </td>
                    <td>
                        <select class="form-control" name="delegaciones_marketing[]">
                            <option value="">General</option>
                            @foreach($delegaciones as $delegacion)
                                <option value="{{ $delegacion->COD }}">{{ $delegacion->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger remove-row-marketing">Eliminar</button>
                    </td>
                </tr>
            `;
            $('#costesTableMarketing tbody').append(newRow);
        });

        // Eliminar fila de la tabla de productos de marketing
        $(document).on('click', '.remove-row-marketing', function() {
            let row = $(this).closest('tr');
            let costeId = $(this).data('id');
    
            // Si tiene un ID, añadirlo a la lista de eliminados
            if (costeId) {
                eliminadosMarketing.push(costeId); 
                $('#eliminadosMarketing').val(eliminadosMarketing.join(',')); // Actualizar el campo oculto
            }
    
            row.remove(); // Eliminar la fila visualmente
        });

        // Código para eliminar costes de marketing
        $('.delete-cost-marketing').click(function(event) {
            event.preventDefault(); // Prevenir el envío del formulario

            const costeId = $(this).data('id');
            
            // Confirmación antes de eliminar
            if (confirm('¿Estás seguro de eliminar este coste de marketing?')) {
                $.ajax({
                    url: `/admin/costes-marketing/${costeId}`, // Asegúrate de que esta URL sea correcta
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}', // Incluye el token CSRF
                    },
                    success: function(response) {
                        // Si la eliminación fue exitosa, elimina la fila
                        $(`tr[data-id="${costeId}"]`).remove();
                        alert('Coste de marketing eliminado correctamente.');
                    },
                    error: function(xhr) {
                        // Manejo de errores
                        alert('No se pudo eliminar el coste de marketing. Por favor, inténtalo de nuevo.');
                    }
                });
            }
        });
    });
</script> --}}
@endsection
