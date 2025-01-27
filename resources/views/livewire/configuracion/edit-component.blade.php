@php
    $EsAdmin = Auth::user()->isAdmin();
    $canEdit = $EsAdmin; //|| $estado == 1;
@endphp
{{-- {{ var_dump($eventoServicios) }} --}}
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Opciones </h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Opciones</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    @php
        $mostrarElemento = Auth::user()->isAdmin();
    @endphp
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="form-group row">
                            <br>
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Opciones de Configuración</h5>
                            </div>
                            
                            
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Texto Legal Facturas</label>
                                <div class="col-sm-12">
                                    <textarea type="text" wire:model="texto_factura" class="form-control"> </textarea>
                                    @error('texto_factura')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Texto Legal Pedidos</label>
                                <div class="col-sm-12">
                                    <textarea type="text" wire:model="texto_pedido" class="form-control"> </textarea>
                                    @error('texto_pedido')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Texto Legal Albaran</label>
                                <div class="col-sm-12">
                                    <textarea type="text" wire:model="texto_albaran" class="form-control"> </textarea>
                                    @error('texto_albaran')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Texto Legal Email</label>
                                <div class="col-sm-12">
                                    <textarea type="text" wire:model="texto_email" class="form-control"> </textarea>
                                    @error('texto_email')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Cuenta del banco</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="cuenta" class="form-control" name="cuenta"
                                        id="cuenta" placeholder="cuenta" @if(!$canEdit) disabled @endif>
                                    @error('cuenta')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-5">
                                <h5>Firma</h5>
                                @if ($firma)
                                    <div class="mb-3 row d-flex justify-content-center col-12">
                                        <div class="col-12">
                                            @if($hasImage)
                                                <img src="{{ asset('storage/photos/' . $firma) }}"
                                                    style="max-width: 100% !important; text-align: center">
                                            @endif
                                        </div>
                                    </div>
                                @endif
                               
                                <div class="mb-3 row d-flex align-items-center">
                                    <div class="col-sm-12">
                                        <input type="file" class="form-control" wire:model="firma" name="firma"
                                            id="firma" placeholder="Imagen del producto...">
                                        @error('nombre')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <button class="btn btn-success mt-2" wire:click="saveFirma()"> Guardar firma</button>
                                    </div>

                                </div>
                                <div>
                                    {{-- <button class="btn btn-danger" wire:click="enviarWhatsappPrueba()">Probar Whatsapp</button> --}}
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Departamentos Proveedores</label>
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2">
                                        <input type="text" wire:model="nombreDepartamento" class="form-control mb-1" name="nombreDepartamento"
                                            id="nombreDepartamento" placeholder="nombre Departamento" @if(!$canEdit) disabled @endif>
                                        @error('departamento')
                                            <span class="text-danger">{{ $message }}</span>
                                            <style>
                                                .nombre {
                                                    color: red;
                                                }
                                            </style>
                                        @enderror
                                    

                                        <button class="btn btn-primary" wire:click="addDepartamento">Añadir</button>
                                    </div>
                                    @if(count($departamentos) > 0)
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($departamentos as $departamento)
                                                    <tr>
                                                        <td>{{ $departamento->nombre }}</td>
                                                        <td>
                                                            <button class="btn btn-danger"
                                                                wire:click="removeDepartamento({{ $departamento->id }})">Eliminar</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                            </div>
                            <div class="col-md-12">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Configurar Almacenes</label>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Almacen</th>
                                            <th>Dirección</th>
                                            <th>Horario</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <!-- Formulario para añadir un nuevo almacén -->
                                        <tr>
                                            <td>
                                                <input type="text" wire:model="newAlmacen.almacen" class="form-control" placeholder="almacen">
                                                @error('newAlmacen.almacen') <span class="text-danger">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <input type="text" wire:model="newAlmacen.direccion" class="form-control" placeholder="Dirección">
                                                @error('newAlmacen.direccion') <span class="text-danger">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <input type="text" wire:model="newAlmacen.horario" class="form-control" placeholder="Horario">
                                                @error('newAlmacen.horario') <span class="text-danger">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <button wire:click="addAlmacen" class="btn btn-success">Añadir</button>
                                            </td>
                                        </tr>
                                        @if(count($almacenes) > 0)
                                            <!-- Fin del formulario para añadir un nuevo almacén -->
                                            @foreach ($almacenes as $almacen)
                                                <tr>
                                                    @if(isset($editableAlmacen['id']) && $editableAlmacen['id'] == $almacen->id)
                                                        <td>
                                                            <input type="text" wire:model="editableAlmacen.almacen" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" wire:model="editableAlmacen.direccion" class="form-control">
                                                        </td>
                                                        <td>
                                                            <input type="text" wire:model="editableAlmacen.horario" class="form-control">
                                                        </td>
                                                        <td>
                                                            <button wire:click="saveAlmacen" class="btn btn-success">Guardar</button>
                                                        </td>
                                                    @else
                                                        <td>{{ $almacen->almacen }}</td>
                                                        <td>{{ $almacen->direccion }}</td>
                                                        <td>{{ $almacen->horario }}</td>
                                                        <td>
                                                            <button wire:click="edit({{ $almacen->id }})" class="btn btn-primary">Editar</button>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    
                    </form>
                </div>
            </div>

            <div class="card col-md-12">
                <div class="card-body">
                    <h5 class="card-title">Retenciones</h5>
                </div>

                <form wire:submit.prevent="addRetencion" class="d-flex align-items-center">
                    <div class="form-group me-2">
                        <label for="nombre" class="me-2">Nombre</label>
                        <input type="text" wire:model="nombre_retencion" class="form-control" id="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-group me-2">
                        <label for="porcentaje" class="me-2">Porcentaje</label>
                        <input type="number" wire:model="porcentaje_retencion" class="form-control" id="porcentaje" placeholder="Porcentaje">
                    </div>
                    <div class="form-group me-2">
                        <label for="dias_retencion" class="me-2">Días de Retención</label>
                        <input type="number" wire:model="dias_retencion" class="form-control" id="dias_retencion" placeholder="Días de Retención">
                    </div>
                    <button type="submit" class="btn btn-primary">Añadir</button>
                </form>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Porcentaje</th>
                                <th>Días de Retención</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($retenciones as $retencion)
                                <tr>
                                    <td>{{ $retencion->nombre }}</td>
                                    <td>{{ $retencion->porcentaje }}</td>
                                    <td>{{ $retencion->dias_retencion }}</td>
                                    <td>
                                        <button wire:click="deleteRetencion({{ $retencion->id }})" class="btn btn-danger">Eliminar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>




            <div class="card col-md-12">
                <div class="card-body">
                    <h5 class="card-title">Historial de Logs del Mes Actual</h5>
        
                    @if(count($logs) > 0)
                        <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                                $('#datatable-logs').DataTable({
                                stateSave: true,
                                    responsive: true,
                                    layout: {
                                        topStart: {
                                            buttons: [
                                                'copy', 'excel', 'pdf', 'colvis'
                                            ]
                                        }
                                    },
                                    lengthChange: false,
                                    pageLength: 10,
                                    buttons: ['copy', 'excelHtml5', 'pdf', 'colvis'],
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
                            <table id="datatable-logs" class="table table-striped" wire:key='{{ rand() }}'>
                                <thead>
                                    <tr>
                                        <th>Acción</th>
                                        <th>Descripción</th>
                                        <th>Fecha de Creación</th>
                                    </tr>
                                </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td>{{ $log->action }}</td>
                                                <td>{{ $log->description }}</td>
                                                <td>{{ $log->created_at->format('d-m-Y H:i:s') }}</td> <!-- Usamos created_at -->
                                            </tr>
                                        @endforeach
                                    </tbody>
                            </table>
                        </div>
                    @else
                        <p>No hay logs para mostrar en el mes actual.</p>
                    @endif
                </div>
            </div>
        
        </div>

        
       


        @if($canEdit)
            <div class="col-md-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Acciones</h5>
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                    Opciones</button>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('whatsapp.mensajes') }}" class="w-100 btn btn-secondary mb-2" id="whatsapp">Ver
                                    Whatsapps</a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('ver-emails.index') }}" class="w-100 btn btn-secondary mb-2" id="emails">Ver
                                    Emails</a> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
        
</div>

    @section('scripts')
    <script src="../assets/js/jquery.slimscroll.js"></script>
    <script>
        $("#alertaGuardar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Pulsa el botón de confirmar para cambiar los datos del cliente.',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('update');
                }
            });
        });
    </script>
    <script src="../assets/js/jquery.slimscroll.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
    @endsection

