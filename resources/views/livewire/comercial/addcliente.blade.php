<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CREAR CLIENTE</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Clientes</a></li>
                    <li class="breadcrumb-item active">Crear cliente</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ csrf_token() }}">
                        
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos del cliente</h5>
                            </div>
                            <div class="col-md-5 mb-3" wire:ignore>
                                <label for="example-text-input" class="col-sm-12 col-form-label">Distribuidor</label>
                                <select wire:model="distribuidor_id" class="form-control select2" id="select2-distribuidor">
                                    <option value="">Selecciona un distribuidor</option>
                                    @foreach ($distribuidores as $distribuidor)
                                        <option value="{{ $distribuidor->id }}">{{ $distribuidor->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 invisible">
                                &nbsp;
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="nombre" class="form-control" name="nombre"
                                        id="nombre" placeholder="Nombre">
                                    @error('nombre')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-md-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">DNI/CIF</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="cif" class="form-control" name="cif"
                                        id="cif" placeholder="DNI o CIF">
                                    @error('cif')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .cif {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de Calle -->
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Dirección</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="direccion" class="form-control" name="direccion"
                                        id="direccion" placeholder="Avenida/Plaza/Calle...">
                                    @error('direccion')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .direccion {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-md-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Provincia</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="provincia" class="form-control" name="provincia"
                                        id="provincia" placeholder="Provincia">
                                    @error('provincia')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .provincia {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Dir Adi 1 -->
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Localidad</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="localidad" class="form-control"
                                        name="localidad" id="localidad" placeholder="Localidad">
                                    @error('localidad')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .localidad {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Código Postal</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="cod_postal" class="form-control"
                                        name="cod_postal" id="cod_postal" placeholder="Código postal">
                                    @error('cod_postal')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .cod_postal {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Teléfono</label>
                                <div class="col-sm-12">
                                    <input type="number" wire:model="telefono" class="form-control" name="telefono"
                                        id="telefono" placeholder="Teléfono">
                                    @error('telefono')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .telefono {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Correo
                                    electrónico</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="email" class="form-control" name="email"
                                        id="email" placeholder="Correo electrónico">

                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .email {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>
                            
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                Cliente</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            $(document).ready(function() {
            $('.select2').select2();
            
            // Sincronizar Select2 con Livewire
            $('#select2-distribuidor').on('change', function (e) {
                @this.set('distribuidor_id', e.target.value);
            });
        });

            $("#alertaGuardar").on("click", () => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Pulsa el botón de confirmar para guardar el cliente.',
                    icon: 'warning',
                    showConfirmButton: true,
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('submit');
                    }
                });
            });
        </script>
    @endsection
</div>
