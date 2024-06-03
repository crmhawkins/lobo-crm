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
    </style>
    
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
                                        infoFiltered: '(filtrado de _MAX_ total registros)',
                                        search: 'Buscar:'
                                    },
            })
        })" wire:key="{{ rand() }}">
            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" wire:key="{{ rand() }}">
                <thead>
                    <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
                        <th>Fecha</th>
                        <th>N.º Lote</th>
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
                            <td>{{ $lote['fecha'] }}</td>
                            <th>{{ $lote['orden_numero'] }}</th>
                            <th>{{ $lote['pedido_id'] }}</th>
                            <th>{{ $lote['almacen'] }}</th>
                            <th>{{ $lote['producto'] }}</th>
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
                        <th>Almacen</th>
                        <th>Producto</th>
                        <th>Cantidad (en Botellas)</th>
                        <th>Cantidad (en Cajas)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($producto_lotes as $loteIndex => $lote)
                        <tr style="background-color:#ececec;">
                            <td>{{ $lote['fecha'] }}</td>
                            <th>{{ $lote['orden_numero'] }}</th>
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
</div>

@section('scripts')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
@endsection
