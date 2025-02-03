
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Giro Bancario</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Operaciones</a></li>
                    <li class="breadcrumb-item active">Giro Bancario</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->

    <div class="row" style="align-items: start !important">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="table-responsive card-body">
                    <div wire:loading.flex class="loader-overlay">
                        <div class="spinner"></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label for="mes">Mes</label>
                            <select wire:model="mes" id="mes" class="form-control">
                                @php
                                    \Carbon\Carbon::setLocale('es');
                                @endphp
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="anio">Año</label>
                            <select wire:model="anio" id="anio" class="form-control" >
                                @foreach(range(Carbon\Carbon::now()->year, 2020) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                        $('#datatable-buttons1').DataTable({
                            responsive: true,
                            layout: {
                                topStart: 'buttons'
                            },
                            lengthChange: false,
                            pageLength: 30,
                            buttons: [
                                {
                                    extend: 'copy',
                                    exportOptions: {
                                        columns: ':not(:last-child)'
                                    }
                                },
                                {
                                    extend: 'excel',
                                    exportOptions: {

                                        columns: ':not(:last-child)',
                                         format: {
                                        body: function (data, row, column, node) {
                                            if ($(node).find('a').length) {
                                                return $(node).text();
                                            }

                                            if ($(node).find('select').length) {

                                                return $(node).find('select option:selected').text();
                                            }
                                            return $(node).find('input').val() || data;                                        
                                            }
                                        }
                                    }
                                },
                                {
                                    extend: 'pdf',
                                    exportOptions: {
                                        columns: ':not(:last-child)'
                                    },
                                    customize: function (doc) {
                                        doc.pageSize = 'A3'; // Cambiar a A3
                                    }
                                },
                                'colvis'
                            ],
                            language: {
                                'lengthMenu': 'Mostrar _MENU_ registros por página',
                                'zeroRecords': 'No se encontraron registros',
                                'info': 'Mostrando página _PAGE_ de _PAGES_',
                                'infoEmpty': 'No hay registros disponibles',
                                'infoFiltered': '(filtrado de _MAX_ total registros)',
                                'search': 'Buscar:',
                            }
                        });
                    })"
                    wire:key='{{ rand() }}'>
                        <table id="datatable-buttons1" class="table table-bordered" wire:key='{{ rand() }}'>
                            <thead>
                                <tr>
                                    <th>Factura</th>
                                    <th>Cliente</th>
                                    <th>Nº Cuenta</th>
                                    <th>Importe</th>
                                    <th>F. Vencimiento</th>
                                    <th>Banco</th>
                                    <th>F. Programación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facturas as $item)
                                    <tr>
                                        <td><a class="btn btn-primary" href="{{ route('facturas.edit', $item->id) }}">{{ $item->numero_factura }}</a></td>
                                        <td><a class="btn btn-primary" href="{{ route('clientes.edit', $item->cliente_id) }}">{{ $item->cliente->nombre }}</a></td>
                                        <td>{{ $item->cliente->cuenta }}</td>
                                        <td>{{ $item->total }}</td>
                                        <td>{{ $item->fecha_vencimiento }}</td>
                                        @if(isset($editing[$item->id]) || $item->giro_bancario)
                                            <td>
                                                <select wire:model="giroData.{{ $item->id }}.banco_id" class="form-control">
                                                    <option value="">Seleccione un banco</option>
                                                    @foreach($bancos as $banco)
                                                        <option value="{{ $banco->id }}" {{ $banco->id == $item->giro_bancario->banco_id ? 'selected' : '' }}>{{ $banco->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                {{-- {{var_dump($giroData)}} --}}
                                                <input type="date" wire:model="giroData.{{ $item->id }}.fecha_programacion" class="form-control">
                                            </td>
                                            <td>
                                                {{$item->estado}}
                                            </td>
                                            <td>
                                                <button wire:click="saveGiro({{ $item->id }})" class="btn btn-success">Guardar</button>
                                            </td>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td>
                                                {{$item->estado}}
                                            </td>
                                            <td>
                                                <button wire:click="editGiro({{ $item->id }})" class="btn btn-primary">Editar</button>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <style>
                        td {
                            border: 1px solid #000 !important;
                        }
                    </style>

                    {{-- {{ $pagares->links() }} --}}
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script src="../assets/js/jquery.slimscroll.js"></script>
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
   
    @endsection

    <style>
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</div>
