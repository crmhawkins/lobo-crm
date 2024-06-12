@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <style>
        @media (max-width: 768px) {
            ul> li:last-child > .dtr-data {
                display: flex;
                gap:10px;
            }

        }
    </style>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">MATERIALES</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Materiales</a></li>
                    <li class="breadcrumb-item active">Ver materiales para productos</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div wire:ignore.self class="modal fade" id="addStock" tabindex="-1" style="background: #00800040" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title ">Sumar Material @if($mercaderiaSeleccionada) <span class="text-info ">{{ $mercaderiaSeleccionada->nombre }}</span> @endif</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Material a sumar:</label>
                    <input type="number" min="1" pattern="^[0-9]+" class="form-control" placeholder="Stock" wire:model="cantidad">
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
                    <h5 class="modal-title">Restar Material @if($mercaderiaSeleccionada) <span class="text-info ">{{ $mercaderiaSeleccionada->nombre }}</span> @endif</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Material a restar:</label>
                    <input type="number" min="1" pattern="^[0-9]+" class="form-control" placeholder="Stock" wire:model="cantidad">
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
                    <h5 class="modal-title text-warning">Rotura de Material @if($mercaderiaSeleccionada) <span class="text-info ">{{ $mercaderiaSeleccionada->nombre }}</span> @endif</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Rotura:</label>
                    <input type="number" min="1" pattern="^[0-9]+" class="form-control" placeholder="Stock" wire:model="cantidad">
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

    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todos los materiales</h4>
                    @if (count($mercaderias) > 0)
                        <div class="col-md-12" wire:ignore>
                            <a href="{{ route('stock-mercaderia.index') }}"
                                class="btn btn-lg btn-primary w-100 mb-1">ENTRADA DE MATERIALES</a>
                            <a href="#" wire:click.prevent="comprobarStockMateriales"
                                class="btn btn-lg btn-primary w-100 mb-1">COMPROBAR STOCK DE MATERIALES</a>
                            <a href="{{ route('mercaderia.create') }}" class="btn btn-lg btn-primary w-100 mb-1">NUEVO
                                MATERIAL PARA PRODUCTO</a>
                            <a href="{{ route('mercaderia.historial') }}" class="btn btn-lg btn-primary w-100">VER HISTORIAL</a>

                            <div  wire:key='{{ time() . 'lobo' }}'>
                                <label for="categoria">Categoría de los materiales</label>
                                <select class="form-control" name="producto" id="select2-categoria" wire:model="categoria_id"
                                    value="{{ $categoria_id }}">
                                    <option value="0">Todos los materiales</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" wire:ignore.self>

                            <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                                $('#datatable-buttons').DataTable({
                                    responsive: true,
                                    layout: {
                                        topStart: 'buttons'
                                    },
                                    lengthChange: false,
                                    pageLength: 30,
                                    buttons: ['copy', 'excel', 'pdf', 'colvis'],
                                    language: {
                                        'lengthMenu': 'Mostrar _MENU_ registros por página',
                                        'zeroRecords': 'No se encontraron registros',
                                        'info': 'Mostrando página _PAGE_ de _PAGES_',
                                        'infoEmpty': 'No hay registros disponibles',
                                        'infoFiltered': '(filtrado de _MAX_ total registros)',
                                        'search': 'Buscar:',
                                    },
                            
                                                    });
                                                })"
                                                wire:key='{{ rand() }}'>                            
                            <table id="datatable-buttons"
                                class="table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Categoría</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Cantidad asignada</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mercaderias as $mercaderia)
                                        <tr>
                                            <td>{{ $mercaderia->nombre }}</td>
                                            <td>{{ $this->getCategoria($mercaderia->categoria_id) }}</td>
                                            <td>{{ $this->getCantidad($mercaderia->id)}}</td>
                                            <td>{{ $this->getCantidadProduccion($mercaderia->id) }}</td>
                                            <td> 
                                                @if($canEdit)
                                                    <a href="mercaderia-edit/{{ $mercaderia->id }}" class="btn btn-primary">Ver/Editar</a> 
                                                @endif
                                                <button class="btn btn-success"  data-toggle="modal" style="align-self: end !important;" data-target="#addStock" wire:click="changeMercaderiaSeleccionadaId({{ $mercaderia->id }})">Sumar</button>
                                                <button class="btn btn-danger" data-toggle="modal" style="align-self: end !important;" data-target="#deleteStock" wire:click="changeMercaderiaSeleccionadaId({{ $mercaderia->id }})">Restar</button>
                                                <button class="btn btn-warning" data-toggle="modal" style="align-self: end !important;" data-target="#roturaStock" wire:click="changeMercaderiaSeleccionadaId({{ $mercaderia->id }})">Rotura</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $("#comprobarStockMateriales").on("click", () => {
        Swal.fire({
            title: '¿Desea comprobar el stock de materiales?',
            text: 'Pulsa el botón de confirmar para continuar.',
            icon: 'info',
            showConfirmButton: true,
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('comprobarStockMateriales');
            }
        });
    });
</script>
<script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
{{-- <script src="../assets/pages/datatables.init.js"></script> --}}
@endsection
