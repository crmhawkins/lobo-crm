@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. VENTAS')

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

        /* Estilos para impresión */
        @media print {
            body {
                font-size: 12px;
                color: #000;
            }
            .btn, .breadcrumb, .page-title-box {
                display: none; /* Ocultar elementos no necesarios en impresión */
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table th, table td {
                border: 1px solid #000;
                padding: 5px;
            }
            .table-responsive {
                overflow: visible;
            }
        }
    </style>
@endsection

@section('content-principal')
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CONTROL PTO. VENTAS</h4>
                <button onclick="exportarTablasAExcel()" class="btn btn-success ">Exportar a Excel</button>
                <a href="{{ route('exportarVentasAPDF', request()->query()) }}" class="btn btn-success">Exportar a PDF</a>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Control Presupuestario</a></li>
                    <li class="breadcrumb-item active">Ventas</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <form method="GET" action="{{ route('control-presupuestario.ventas') }}">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por número de factura o cliente" value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <input type="date" name="fechaMin" class="form-control" value="{{ request('fechaMin') }}" placeholder="Fecha mínima">
                    </div>

                    <div class="col-md-3">
                        <input type="date" name="fechaMax" class="form-control" value="{{ request('fechaMax') }}" placeholder="Fecha máxima">
                    </div>

                    <div class="col-md-3 mt-2">
                        <select name="perPage" class="form-control" style="width: auto; display: inline-block;">
                            <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10 por página</option>
                            <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 por página</option>
                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 por página</option>
                        </select>
                    </div>

                    <div class="col-md-3 mt-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Tabla principal con scroll horizontal -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Número</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Delegación</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Total</th>
                            <th scope="col">Observaciones</th>
                            <!-- Generar columnas para cada producto -->
                            @foreach($productos as $producto)
                                <th scope="col">{{ $producto->nombre }}</th>
                            @endforeach
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facturas as $factura)
                            <tr>
                                <td>{{ $factura->created_at->format('d-m-Y') }}</td>
                                <td>{{ $factura->numero_factura }}</td>
                                <td>{{ $factura->cliente->nombre }}</td>
                                <td>{{ $factura->cliente->delegacion->nombre ?? 'No definido' }}</td>
                                <td>{{ $factura->created_at->format('d-m-Y') }}</td>
                                @if($factura->factura_id)
                                  @php
                                    if($factura->hasIva){
                                        $totalFactura = $factura->total - $factura->facturaNormal->total ;
                                    }else{
                                        $totalFactura = $factura->precio - $factura->facturaNormal->precio;
                                    }
                                  @endphp
                                    <td>{{ number_format($totalFactura, 2) }}€ </td>
                                @else
                                    @if($factura->hasIva)
                                        <td>{{ number_format($factura->total, 2) }}€</td>
                                    @else
                                        <td>{{ number_format($factura->precio, 2) }}€</td>
                                    @endif

                                @endif
                                <td>{{ $factura->descripcion  }}</td>

                                <!-- Mostrar las cantidades para cada producto -->
                                @foreach($productos as $producto)
                                    @php
                                        $cantidadProducto = 0;
                                         // Si es una factura rectificativa, restamos las unidades descontadas
                                         if ($factura->factura_id && $factura->productosFacturas) {
                                            $productoFactura = $factura->productosFacturas->firstWhere('producto_id', $producto->id);
                                            if ($productoFactura) {
                                                $cantidadProducto = -$productoFactura->cantidad;
                                            }
                                        }else if ($factura->pedido && $factura->pedido->productosPedido) {
                                            $productoPedido = $factura->pedido->productosPedido->firstWhere('producto_pedido_id', $producto->id);
                                            if ($productoPedido) {
                                                $cantidadProducto = $productoPedido->unidades;
                                            }
                                        }
                                        
                                       
                                    @endphp
                                    <td>{{ $cantidadProducto }}</td>
                                @endforeach

                                <td>
                                    <a href="{{ route('facturas.edit', ['id' => $factura->id]) }}" class="btn btn-primary btn-sm fw-bold"> Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Añadir la paginación -->
            <div class="d-flex justify-content-center">
                {{ $facturas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

     <!-- Segunda tabla con el total de botellas vendidas y el total en euros por cada producto -->
    <div class="row mt-5 mb-5">
        <div class="col-md-12">
            <h4 class="mt-0 header-title">Totales de Ventas por Producto</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <!-- Generar columnas para cada producto -->
                            @foreach($productos as $producto)
                                <th scope="col">{{ $producto->nombre }}</th>
                            @endforeach
                            <th scope="col">Total General</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <!-- Mostrar las unidades vendidas para cada producto -->
                            @foreach($productos as $producto)
                                <td>{{ $producto->total_unidades_vendidas }}</td>
                            @endforeach
                            <td><strong>{{ number_format($totalEurosFacturas, 2) }}€</strong></td>
                        </tr>
                        {{-- <tr>
                            <!-- Mostrar el total en euros para cada producto -->
                            @foreach($productos as $producto)
                                <td>{{ number_format($producto->total_euros_vendidos, 2)  }}€</td>
                            @endforeach
                            <!-- Mostrar el total de todas las facturas al final -->
                            
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
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
            XLSX.writeFile(wb, 'control_presupuestario_ventas.xlsx');
        }
    </script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        async function exportarTablasAPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: 'landscape', // Cambia a 'portrait' si prefieres
                unit: 'pt',
                format: 'a4'
            });
    
            // Seleccionar la tabla que deseas exportar
            const table = document.querySelector('.table-responsive table');
    
            // Usar html2canvas para capturar la tabla como imagen
            await html2canvas(table, {
                scale: 2, // Aumenta la escala para mejorar la calidad
                useCORS: true // Permite cargar imágenes de otros dominios
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const imgProps = doc.getImageProperties(imgData);
                const pdfWidth = doc.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
    
                // Añadir la imagen al PDF
                doc.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            });
    
            // Descargar el PDF
            doc.save('ventas.pdf');
        }
    </script>
</div>
@endsection
