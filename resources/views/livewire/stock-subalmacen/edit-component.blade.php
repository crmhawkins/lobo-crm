@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <style>
        @media(max-width: 760px){
                .unidades > div > div{
                    min-width: 100px;
                }
        }
        </style>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR STOCK</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Stock</a></li>
                    <li class="breadcrumb-item active">Editar stock</li>
                </ol>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="addStock" tabindex="-1" style="background: #00800040" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title ">Sumar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Stock a sumar:</label>
                    <input type="number" min="1" pattern="^[0-9]+" class="form-control" placeholder="Stock" wire:model="addStockItem">
                    <br>
                    <label>Motivo:</label>
                    <textarea class="form-control" wire:model="motivo"></textarea>


                    <button class="btn btn-success mt-2" wire:click="addStock" data-dismiss="modal">Sumar</button>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" style="background:#ff000040" id="deleteStock" tabindex="-1" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restar Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Stock a restar:</label>
                    <input type="number" min="1" pattern="^[0-9]+" class="form-control" placeholder="Stock" wire:model="deleteStockItem">
                    <br>
                    <label>Motivo:</label>
                    <textarea class="form-control" wire:model="motivo"></textarea>

                    <button class="btn btn-danger mt-2" wire:click="deleteStock" data-dismiss="modal">Restar</button>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" style="background: #ffff0040" id="roturaStock" tabindex="-1" role="dialog">
        <div class="modal-dialog "
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-warning">Rotura de Stock</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Rotura:</label>
                    <input type="number" min="1" pattern="^[0-9]+" class="form-control" placeholder="Stock" wire:model="roturaStockItem">
                    <br>
                    <label>Motivo:</label>
                    <textarea class="form-control" wire:model="motivo"></textarea>
                    <button class="btn btn-success mt-2" wire:click="roturaStock" data-dismiss="modal">Rotura</button>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="roturaitems" tabindex="-1" style="background: #00800040;" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important; width:140%;">
            <div class="modal-content" style="width: 150%;">
                <div class="modal-header">
                    <h5 class="modal-title ">Ver Roturas</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if(count($roturas) > 0)
                <div class="modal-body" x-data="{}" x-init="$nextTick(() => {
                    $('#datatable-rotura').DataTable({
                        responsive: true,
                        lengthChange: false,
                        pageLength: 5,
                        info: false,
                        language: {
                            lengthMenu: 'Mostrar _MENU_ registros por página',
                            zeroRecords: 'No se encontraron registros',
                            info: 'Mostrando página _PAGE_ de _PAGES_',
                            infoEmpty: 'No hay registros disponibles',
                            infoFiltered: '(filtrado de _MAX_ total registros)',
                            search: 'Buscar:'
                        },
                        
                    });
                })" wire:key='{{ rand() }}'>
                
                    <table id="datatable-rotura" style="width: 100%"  wire:key='{{ rand() }}'>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>User</th>
                                <th>Cantidad</th>
                                <th>Motivo</th>
                                <th>Almacen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roturas as $rotura )

                            <tr>
                                <td>{{$rotura->fecha}}</td>
                                <td>{{$this->getNombreUsuario($rotura->user_id)}}</td>
                                <td>{{$rotura->cantidad}}</td>
                                <td>{{$rotura->observaciones}}</td>
                                <td>{{$this->getNombreAlmacen($rotura->almacen_id)}}</td>
                            </tr>
                                
                            @endforeach
                        </tbody>

                    </table>
                    
                  
                </div>
                @endif 
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="modificacionesitems" tabindex="-1" style="background: #00800040;" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important; width:140%;">
            <div class="modal-content" style="width: 150%;">
                <div class="modal-header">
                    <h5 class="modal-title ">Ver Modificaciones</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if(count($modificaciones) > 0)
                <div class="modal-body" x-data="{}" x-init="$nextTick(() => {
                    $('#datatable-modificacion').DataTable({
                        responsive: true,
                        lengthChange: false,
                        pageLength: 5,
                        info: false,
                        language: {
                            lengthMenu: 'Mostrar _MENU_ registros por página',
                            zeroRecords: 'No se encontraron registros',
                            info: 'Mostrando página _PAGE_ de _PAGES_',
                            infoEmpty: 'No hay registros disponibles',
                            infoFiltered: '(filtrado de _MAX_ total registros)',
                            search: 'Buscar:'
                        },
                        
                    });
                })" wire:key='{{ rand() }}'>
                
                    <table id="datatable-modificacion" style="width: 100%"  wire:key='{{ rand() }}'>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>User</th>
                                <th>Tipo</th>
                                <th>Cantidad</th>
                                <th>Motivo</th>
                                <th>Almacen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modificaciones as $modificacion )

                            <tr>
                                <td>{{$modificacion->fecha}}</td>
                                <td>{{$this->getNombreUsuario($modificacion->user_id)}}</td>
                                <td>{{$modificacion->tipo}}</td>
                                <td>{{$modificacion->cantidad}}</td>
                                <td>{{$modificacion->motivo}}</td>
                                <td>{{$this->getNombreAlmacen($modificacion->almacen_id)}}</td>
                            </tr>
                                
                            @endforeach
                        </tbody>

                    </table>
                    
                  
                </div>
                @endif 
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                básicos </h5>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-11">
                            @if (isset($almacenActual))
                            <h4> Almacén: {{ $almacenActual->almacen }}
                            @else
                            <h4> Almacén: No asignado
                            @endif
                            </h4>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-3">
                            <label for="Qr">Identificador del QR</label>
                            <input type="text" wire:model="qr_id" class="form-control" disabled>
                        </div>
                        <div class="form-group col-md-3" wire:ignore>
                            <div x-data="" x-init="$('#select2-estado').select2();
                            $('#select2-estado').on('change', function(e) {
                                var data = $('#select2-estado').select2('val');
                                @this.set('estado', data);
                            });">
                                <label for="Estado">Estado</label>
                                <select class="form-control" name="estado" id="select2-estado" wire:model="estado" @if(!$canEdit) disabled @endif>
                                    <option value="0">Stock completo</option>
                                    <option value="1">Stock parcial</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha">Fecha</label>
                            <input type="date" wire:model="fecha" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-10">
                            <label for="fecha">Observaciones</label>
                            <textarea wire:model="observaciones" class="form-control" @if(!$canEdit) disabled @endif></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important;padding-bottom: 10px !important;display: flex !important;flex-direction: row;justify-content: space-between;">
                                Stock</h5>
                            <div class="form-group col-md-12">
                                <div class="table-responsive">
                                    <table class="table  table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>N.º interno</th>
                                                <th>N.º Lote</th>
                                                <th>Cantidad</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                    <td width="20%">
                                                        {{$this->getNombreTabla($this->stockentrante->producto_id) }}
                                                    </td>
                                                    <td width="20%">
                                                        {{$this->stockentrante->lote_id  }}
                                                    </td>
                                                    <td width="20%">
                                                        {{$this->stockentrante->orden_numero }}
                                                    </td>
                                                    <td width="25%" class="unidades">
                                                        <div class="row align-items-center">
                                                            <div class="col-8 text-end">
                                                                <input type="number" class="form-control" wire:model="cantidad" disabled >
                                                            </div>
                                                            <div class="col-4 text-start">
                                                                <p class="my-auto">Unidades</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td width="100%" class="d-flex gap-2 flex-wrap">
                                                        <button class="btn btn-success"  data-toggle="modal" style="align-self: end !important;" data-target="#addStock">Sumar</button>
                                                        <button class="btn btn-danger" data-toggle="modal" style="align-self: end !important;" data-target="#deleteStock" >Restar</button>
                                                        <button class="btn btn-warning" data-toggle="modal" style="align-self: end !important;" data-target="#roturaStock" >Rotura</button>
                                                    </td>
                                                </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if(count($roturas) > 0 || count($modificaciones) > 0)
        <div class="col-md-3" style="width: 23vw !important;">
            <div class="card m-b-30 position-fixed" style="width: -webkit-fill-available">
                <div class="card-body">
                    <h5>Opciones </h5>
                    {{-- <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" wire:click.prevent="update">Actualizar stock</button>
                        </div>
                    </div> --}}
                    @if(count($roturas) > 0)
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-warning mb-2" data-toggle="modal" data-target="#roturaitems" >Ver roturas</button>
                            </div>
                        </div>
                    @endif
                    @if(count($modificaciones) > 0)
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-info mb-2" data-toggle="modal" data-target="#modificacionesitems" >Ver Modificaciones</button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        <style>
            fieldset.scheduler-border {
                border: 1px groove #ddd !important;
                padding: 0 1.4em 1.4em 1.4em !important;
                margin: 0 0 1.5em 0 !important;
                -webkit-box-shadow: 0px 0px 0px 0px #000;
                box-shadow: 0px 0px 0px 0px #000;
            }

            table {
                border: 1px black solid !important;
            }

            th {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 1px black solid !important;
            }

            th.header {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 2px black solid !important;
            }

            td.izquierda {
                border-left: 1px black solid !important;

            }

            td.derecha {
                border-right: 1px black solid !important;

            }

            td.suelo {}
        </style>
        <script>
            window.addEventListener('initializeMapKit', () => {
                fetch('/admin/service/jwt')
                    .then(response => response.json())
                    .then(data => {
                        mapkit.init({
                            authorizationCallback: function(done) {
                                done(data.token);
                            }
                        });
                        // Aquí puedes inicializar tu mapa u otras funcionalidades relacionadas
                    });
            });
        </script>
    </div>

    @section('scripts')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
        </script>
    @endsection
