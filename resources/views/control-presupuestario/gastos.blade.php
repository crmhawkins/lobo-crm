@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. GASTOS')

@section('head')
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
                <h4 class="page-title">CONTROL PTO. GASTOS</h4>
                <button onclick="exportarTablasAExcel()" class="btn btn-success mb-4">Exportar a Excel</button>
                <a href="{{ route('exportarGastosAPDF') }}" class="btn btn-success mb-4">Exportar a PDF</a>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Control Presupuestario</a></li>
                    <li class="breadcrumb-item active">Gastos</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Tabla principal con scroll horizontal -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">Relación Gasto</th>
                            @foreach($delegaciones as $delegacion)
                                <th colspan="12">{{ $delegacion->nombre }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($delegaciones as $delegacion)
                                @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                                    <th colspan="3">Trimestre {{ $trimestre }}</th>
                                @endfor
                            @endforeach
                        </tr>
                        <tr>
                            <th></th>
                            @php
                                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                            @endphp
                            @foreach($delegaciones as $delegacion)
                                @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                                    @foreach([1, 2, 3] as $mes)
                                        <th>{{ $meses[($trimestre - 1) * 3 + $mes - 1] }}</th>
                                    @endforeach
                                @endfor
                            @endforeach
                        </tr>
                        <!-- Fila para los totales por mes y delegación -->
                        <tr>
                            <td><strong>Total Mensual</strong></td>
                            @foreach($delegaciones as $delegacion)
                                @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                                    @foreach([1, 2, 3] as $mes)
                                        @php
                                            $mesReal = ($trimestre - 1) * 3 + $mes;
                                            $totalMesDelegacion = 0;
                                            foreach ($proveedores as $proveedor) {
                                                $totalMesDelegacion += $gastosPorTrimestre[$trimestre][$mesReal][$delegacion->nombre][$proveedor] ?? 0;
                                            }
                                        @endphp
                                        <th><strong>{{ number_format($totalMesDelegacion, 2) }}€</strong></th>
                                    @endforeach
                                @endfor
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedores as $proveedor)
                            <tr>
                                <td>{{ $proveedor }}</td>
                                @foreach($delegaciones as $delegacion)
                                    @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                                        @foreach([1, 2, 3] as $mes)
                                            @php
                                                $mesReal = ($trimestre - 1) * 3 + $mes;
                                                $totalMes = $gastosPorTrimestre[$trimestre][$mesReal][$delegacion->nombre][$proveedor] ?? 0;
                                            @endphp
                                            <td>{{ number_format($totalMes, 2) }}€</td>
                                        @endforeach
                                    @endfor
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        function exportarTablasAExcel() {
            var wb = XLSX.utils.book_new();
            document.querySelectorAll('.container-fluid .table-responsive').forEach((tableContainer, index) => {
                var tableName = 'Gastos Trimestre ' + (index + 1);
                var table = tableContainer.querySelector('table');
                var ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, tableName);
            });
            XLSX.writeFile(wb, 'Gastos.xlsx');
        }
    </script>
</div>
@endsection 