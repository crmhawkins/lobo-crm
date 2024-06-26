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
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
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
                            

                            <div class="card-body">
                                <h5>Firma</h5>
                                @if ($firma)
                                    <div class="mb-3 row d-flex justify-content-center">
                                        <div class="col">
                                            @if($hasImage)
                                                <img src="{{ asset('storage/photos/' . $firma) }}"
                                                    style="max-width: 100% !important; text-align: center">
                                            @endif
                                        </div>
                                    </div>
                                @endif
                               
                                <div class="mb-3 row d-flex align-items-center">
                                    <div class="col-sm-4">
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
                            <div class="col-md-5">
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
    @endsection

