<div class="container-fluid pb-5">
    <style>    
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
        }

        tr.left-aligned > th, td{
            text-align: right !important;
        }

        th,
        td {
            border: 0px solid black;
            padding: 10px;
            text-align: center;
        }

        .header-1 {
            background-color: #0196eb;
            color: #fff;
            font-size: 90%;
        }

        .header,
        .footer {
            width: 100%;
        }

        .loader {
            border: 16px solid #f3f3f3; /* Light grey */
            border-top: 16px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <!-- Loader -->
    <div wire:loading class="loader"></div>

    <div class="row">
        <div class="col-md-12 d-flex flex-wrap">
            <div class="form-group col-md-4">
                <label for="stock">Salida/Entrada</label>
                <select wire:model="isEntrada" class="form-control" id="stock">
                    <option value="0">Saliente</option>
                    <option value="1">Entrante</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="stock">Producto</label>
                <select wire:model="producto_id" class="form-control" id="stock">
                    <option value="0">Todos</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="stock">Almacen</label>
                <select wire:model="almacen_id" class="form-control" id="stock">
                    <option value="0">Todos</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="tipo">Tipo</label>
                <select wire:model="tipo" class="form-control" id="tipo">
                    <option value="0">Todos</option>
                    <option value="Venta">Venta</option>
                    <option value="Modificación">Modificación</option>
                    <option value="Salida">Salida</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="mes">Mes</label>
                <select wire:model="mes" class="form-control" id="mes">
                    <option value="0">Todos</option>
                    @php
                        $meses = [
                            1 => 'Enero',
                            2 => 'Febrero',
                            3 => 'Marzo',
                            4 => 'Abril',
                            5 => 'Mayo',
                            6 => 'Junio',
                            7 => 'Julio',
                            8 => 'Agosto',
                            9 => 'Septiembre',
                            10 => 'Octubre',
                            11 => 'Noviembre',
                            12 => 'Diciembre'
                        ];
                    @endphp
                    @foreach ($meses as $numero => $nombre)
                        <option value="{{ $numero }}">{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="anio">Año</label>
                <select wire:model="anio" class="form-control" id="anio">
                    @foreach (range(date('Y'), date('Y') - 10) as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="anio">Comerciales</label>
                <select wire:model="comercial_id" class="form-control" id="comercial_id">
                    <option value="0">Todos</option>
                    @foreach ($comerciales as $comercial)
                        <option value="{{ $comercial->id }}">{{ $comercial->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="anio">Delegaciones</label>
                <select wire:model="delegacion_id" class="form-control" id="delegacion_id">
                    <option value="-1">Todas</option>
                    @foreach ($delegaciones as $delegacion)
                        <option value="{{ $delegacion->COD }}">{{ $delegacion->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    
    <table  class="header" wire:key="{{ rand() }}">
        <tr width="100%">
            <td width="40%" style="text-align: left !important"></td>
            <td width="20%">&nbsp;</td>
            <td class="bold" width="40%" style="text-align: right !important">
                    <h1 style="display: inline; color:#0196eb; font-weight:bolder;">Historial de Stock @if($isEntrada) Entrante  @else Saliente @endif</h1><br>
            </td>
        </tr>

    </table>
    <div style="margin-left: -10%; width: 250%; border-bottom: 2px solid #bbbbbb"></div>
    <br>
    @if(!$isEntrada)  
        <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
            $('#datatable-buttons').DataTable({
                responsive: true,
                layout: {
                    topStart: {
                        buttons: [
                            {
                                extend: 'copyHtml5',
                                exportOptions: { orthogonal: 'export' }
                            },
                            {
                                extend: 'excelHtml5',
                                exportOptions: { orthogonal: 'export' },
                            },
                            {
                                extend: 'pdfHtml5',
                                exportOptions: { orthogonal: 'export' }
                            },
                            {
                                extend: 'colvis',
                                columns: ':not(.noVis)'
                            }
                        ]
                    }
                },
                lengthChange: false,
                pageLength: 30,
                buttons: ['copy', 'excelHtml5', 'pdf', 'colvis'],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    zeroRecords: 'No se encontraron registros',
                    info: 'Mostrando página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    emptyTable: 'No hay registros disponibles',
                    infoFiltered: '(filtrado de _MAX_ total registros)',
                    search: 'Buscar:'
                },
            })
        })" wire:key="{{ rand() }}">
            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="max-width:100%;" wire:key="{{ rand() }}">
                <thead>
                    <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
                        <th>Fecha</th>
                        <th>N.º Lote</th>
                        <th>QR</th>
                        <th>Pedido</th>
                        <th>Almacen</th>
                        <th>Producto</th>
                        <th>Cantidad (en Botellas)</th>
                        <th>Cantidad (en Cajas)</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($producto_lotes as $loteIndex => $lote)
                        <tr style="background-color:#ececec;">
                            <td data-sort='{{ $lote['order_date'] }}' >{{ $lote['fecha'] }}</td>
                            <td>{{ $lote['orden_numero'] }}</td>
                            <td>{{ $lote['qr'] }}</td>
                            <td>
                                @if($lote['pedido_id'] != null && $lote['pedido_id'] != '-')
                                    <a class="badge badge-info" href="{{ route('pedidos.edit', $lote['pedido_id']) }}" > 
                                        @if($this->isPedidoMarketing($lote['pedido_id'])) 
                                            {{ config('app.departamentos_pedidos')['Marketing']['pedido'] }}
                                        @else 
                                            {{ config('app.departamentos_pedidos')['General']['pedido'] }}
                                        @endif
                                        {{ $lote['pedido_id'] }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $lote['almacen'] }}</td>
                            <td>{{ $lote['producto'] }}</td>
                            <td>{{ $lote['cantidad'] }}</td>
                            <td>{{ $lote['cajas']}}</td>
                            <td>{{ $lote['tipo'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
            $('#datatable-buttons2').DataTable({
                responsive: true,
                layout: {
                    topStart: {
                        buttons: [
                            {
                                extend: 'copyHtml5',
                                exportOptions: { orthogonal: 'export' }
                            },
                            {
                                extend: 'excelHtml5',
                                exportOptions: { orthogonal: 'export', columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] },
                            },
                            {
                                extend: 'pdfHtml5',
                                exportOptions: { orthogonal: 'export' }
                            },
                            {
                                extend: 'colvis',
                                columns: ':not(.noVis)'
                            }
                        ]
                    }
                },
                lengthChange: false,
                pageLength: 30,
                buttons: ['copy', 'excelHtml5', 'pdf', 'colvis'],
                language: {
                    lengthMenu: 'Mostrar _MENU_ registros por página',
                    zeroRecords: 'No se encontraron registros',
                    info: 'Mostrando página _PAGE_ de _PAGES_',
                    infoEmpty: 'No hay registros disponibles',
                    emptyTable: 'No hay registros disponibles',
                    infoFiltered: '(filtrado de _MAX_ total registros)',
                    search: 'Buscar:'
                },
            })
        })" wire:key="{{ rand() }}">
            <table id="datatable-buttons2" class="table table-striped table-bordered dt-responsive nowrap" wire:key="{{ rand() }}">
                <thead>
                    <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
                        <th>Fecha</th>
                        <th>N.º Lote</th>
                        <th>QR</th>
                        <th>Almacen</th>
                        <th>Producto</th>
                        <th>Cantidad (en Botellas)</th>
                        <th>Cantidad (en Cajas)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($producto_lotes as $loteIndex => $lote)
                        <tr style="background-color:#ececec;">
                            <td data-sort='{{ $lote['order_date'] }}'>{{ $lote['fecha'] }}</td>
                            <th>{{ $lote['orden_numero'] }}</th>
                            <th>{{ $lote['qr'] }}</th>
                            <th>{{ $lote['almacen'] }}</th>
                            <th>{{ $lote['producto'] }}</th>
                            <td>{{ $lote['cantidad'] }}</td>
                            <td>{{ $lote['cajas']}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    <style>
        .content-page{
            overflow: hidden !important;
        }
    </style>
</div>

@section('scripts')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.datatables.net/datetime-moment/1.1.0/js/dataTables.dateTimeMoment.min.js"></script>
@endsection
