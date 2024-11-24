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
        <button onclick="exportarTablasAExcel()" class="btn btn-success mb-4">Exportar a Excel</button>
        <button onclick="exportarTablasAPDF()" class="btn btn-success mb-4">Exportar a PDF</button>

        <!-- Loader -->
        <div id="loader" style="display: none; text-align: center;">
            <p>Generando PDF, por favor espera...</p>
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    function exportarTablasAExcel() {
        // Crear un nuevo libro de trabajo
        var wb = XLSX.utils.book_new();

        // Seleccionar todas las tablas dentro del contenedor principal
        document.querySelectorAll('.container .table').forEach((table, index) => {
            // Obtener el nombre de la tabla desde el h3 anterior
            var tableNameElement = table.previousElementSibling;
            console.log(table);
            while (tableNameElement && tableNameElement.tagName !== 'H3') {
                tableNameElement = tableNameElement.previousElementSibling;
            }
            var tableName = tableNameElement ? tableNameElement.textContent.trim() : 'Tabla ' + (index + 1);

            // Limpiar el nombre de la hoja eliminando caracteres no permitidos
            tableName = tableName.replace(/[:\\\/?*\[\]]/g, '');

            // Truncar el nombre de la hoja si es necesario
            if (tableName.length > 28) {
                tableName = tableName.substring(0, 28);
            }

            // Asegurar que el nombre de la hoja sea único
            tableName += ' ' + (index + 1);

            // Convertir la tabla HTML a una hoja de cálculo
            var ws = XLSX.utils.table_to_sheet(table);

            // Añadir la hoja de cálculo al libro de trabajo
            XLSX.utils.book_append_sheet(wb, ws, tableName);
        });

        // Exportar el libro de trabajo a un archivo Excel
        XLSX.writeFile(wb, 'ventas_por_productos.xlsx');
    }

    async function exportarTablasAPDF() {
        // Mostrar el loader
        document.getElementById('loader').style.display = 'block';

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF();

        // Seleccionar todas las tablas dentro del contenedor principal
        const tables = document.querySelectorAll('.container .table');
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
        pdf.save('ventas_por_productos.pdf');

        // Ocultar el loader
        document.getElementById('loader').style.display = 'none';
    }
</script>
