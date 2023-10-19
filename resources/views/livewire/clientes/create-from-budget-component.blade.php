{{-- {{ var_dump($eventoServicios) }} --}}
{{-- @section('content') --}}
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CREAR CLIENTE DESDE PRESUPUESTO @if(session('datos')) {{session('datos')['nPresupuesto']}}@endif</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Presupuesto</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Crear presupuesto</a></li>
                    <li class="breadcrumb-item active">Crear cliente desde presupuesto</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->

    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="submit">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ csrf_token() }}">
                        <div class="form-group row">
                            <br>
                            <div class="col-sm-12">
                                <h5 class="ms-3" style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Tipo de cliente</h5>
                            </div>
                            <div class="col-sm-1 d-inline-flex align-items-center ms-5">
                                <input class="form-check-input mt-0" wire:model="tipo_cliente" type="radio" value="1" id="check1">
                                <label for="check1" class=" col-form-label">Empresa</label>
                            </div>
                            <div class="col-sm-1 d-inline-flex align-items-center ms-5">
                                <input class="form-check-input mt-0" wire:model="tipo_cliente" type="radio" value="0" id="check2">
                                <label for="check2" class=" col-form-label">Particular</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <h5 class="ms-3" style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos del cliente</h5>
                            </div>
                            @if($tipo_cliente != 1)
                            <div class="col-sm-2">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Tratamiento</label>
                                <div class="col-sm-10">
                                    <select class="input-group-text" name="trato" required id="trato" wire:model="trato">
                                        <option class="dropdown-item" disabled>Trato</option>
                                        <option class="dropdown-item" value="M">M</option>
                                        <option class="dropdown-item" value="Melle">Melle</option>
                                        <option class="dropdown-item" value="Mme">Mme</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre" aria-label="Nombre" placeholder="Nombre">
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
                            <div class="col-sm-6">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Apellidos</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model="apellido" class="form-control" name="apellido" id="apellido" placeholder="Apellidos">
                                    @error('apellido')
                                    <span class="text-danger">{{ $message }}</span>

                                    <style>
                                        .apellido {
                                            color: red;
                                        }
                                    </style>
                                    @enderror
                                </div>
                            </div>
                            @else
                            <div class="col-sm-12">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre de la empresa</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre" aria-label="Nombre" placeholder="Nombre">
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
                            @endif
                            <!-- NIF/DNI -->
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">NIF/DNI</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="nif" class="form-control" name="nif" id="nif" placeholder="Nif">
                                        @error('nif')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .nif {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>
                                @if($tipo_cliente != 0)
                                <div class="col-sm-3">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Código Órgano Gestor</label>
                                    <div class="col-sm-12">
                                        <input type="text" wire:model="codigo_organo_gestor" class="form-control" name="codigo_organo_gestor" id="codigo_organo_gestor" aria-label="Nombre" placeholder="Nombre">
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
                                <div class="col-sm-3">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Código Unidad Tramitadora</label>
                                    <div class="col-sm-12">
                                        <input type="text" wire:model="codigo_unidad_tramitadora" class="form-control" name="codigo_unidad_tramitadora" id="codigo_unidad_tramitadora" aria-label="Nombre" placeholder="Nombre">
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
                                <div class="col-sm-3">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Códigos Oficina Contable</label>
                                    <div class="col-sm-12">
                                        <input type="text" wire:model="codigo_oficina_contable" class="form-control" name="codigo_oficina_contable" id="codigo_oficina_contable" aria-label="Nombre" placeholder="Nombre">
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
                                @endif
                            </div>

                            <!-- Tipo de Calle -->
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Tipo de
                                        Calle</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="tipoCalle" class="form-control" name="tipoCalle" id="tipoCalle" placeholder="Avenida/Plaza/Calle...">
                                        @error('tipoCalle')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .tipoCalle {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Via</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="calle" class="form-control" name="calle" id="calle" placeholder="Calle">
                                        @error('calle')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .calle {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Nº</label>
                                    <div class="col-sm-10">
                                        <input type="number" wire:model="numero" class="form-control" name="numero" id="numero" placeholder="1">
                                        @error('numero')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .numero {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Dir Adi 1 -->
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Dir Adi 1</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="direccionAdicional1" class="form-control" name="direccionAdicional1" id="direccionAdicional1" placeholder="Bloque/Letra...">
                                    </div>
                                </div>

                                <!-- Dir Adi 2 -->
                                <div class="col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Dir Adi 2</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="direccionAdicional2" class="form-control" name="direccionAdicional2" id="direccionAdicional2" placeholder="Bloque/Letra...">
                                    </div>
                                </div>

                                <!-- Dir Adi 3 -->
                                <div class="col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Dir Adi 3</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="direccionAdicional3" class="form-control" name="direccionAdicional3" id="direccionAdicional3" placeholder="Bloque/Letra...">
                                    </div>
                                </div>
                            </div>


                            <!-- CP -->
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">CP</label>
                                    <div class="col-sm-10">
                                        <input type="number" wire:model="codigoPostal" class="form-control" name="codigoPostal" id="codigoPostal" placeholder="XXXXX">
                                        @error('codigoPostal')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .codigoPostal {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Ciudad -->
                                <div class="col-md-6">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Ciudad</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="ciudad" class="form-control" name="ciudad" id="ciudad" placeholder="Ciudad">
                                        @error('ciudad')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .ciudad {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <!-- Telefono -->
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="tlf1" class="col-sm-12 col-form-label">Telefono</label>
                                    <div class="col-sm-10">
                                        <input type="number" wire:model="tlf1" class="form-control" name="tlf1" id="tlf1" placeholder="XXXXXXXXX">
                                        @error('tlf1')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .tlf1 {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Telefono Secundario -->
                                <div class="col-md-4">
                                    <label for="tlf2" class="col-sm-12 col-form-label">Telefono Secundario</label>
                                    <div class="col-sm-10">
                                        <input type="number" wire:model="tlf2" class="form-control" name="tlf2" id="tlf2" placeholder="Opcional">
                                    </div>
                                </div>

                                <!-- Telefono Adicional -->
                                <div class="col-md-4">
                                    <label for="tlf3" class="col-sm-12 col-form-label">Telefono Adicional</label>
                                    <div class="col-sm-10">
                                        <input type="number" wire:model="tlf3" class="form-control" name="tlf3" id="tlf3" placeholder="Opcional">
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="email1" class="col-sm-12 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="email1" class="form-control" name="email1" id="email1" placeholder="Email@email.com">
                                        @error('email1')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .email1 {
                                                color: red;
                                            }
                                        </style>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email Secundario -->
                                <div class="col-md-4">
                                    <label for="email1" class="col-sm-12 col-form-label">Email Secundario</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="email2" class="form-control" name="email2" id="email2" placeholder="email@email.com">
                                    </div>
                                </div>

                                <!-- Email Adicional -->
                                <div class="col-md-4">
                                    <label for="email1" class="col-sm-12 col-form-label">Email Adicional</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="email3" class="form-control" name="email3" id="email3" placeholder="Email@email.com">
                                    </div>
                                </div>
                            </div> <!-- Confirmacion Email -->
                            <div class="form-group row">
                                <div class="col-sm-12 d-inline-flex align-items-center ms-5">
                                    <input class="form-check-input mt-0" wire:model="confPostal" type="checkbox" name="confPostal" id="confPostal">
                                    <label for="confPostal" class="col-form-label">¿Deseas recibir una confirmación
                                        por correo postal?</label>
                                    {{-- <span class="input-group-text">Confirmacion Postal</span> --}}
                                </div>
                                <div class="col-sm-12 d-inline-flex align-items-center ms-5">
                                    <input class="form-check-input mt-0" wire:model="confSms" type="checkbox" id="confSms">
                                    <label for="confSms" class="col-form-label">¿Deseas recibir una confirmación por
                                        SMS?</label>
                                </div>
                                <div class="col-sm-12 d-inline-flex align-items-center ms-5">
                                    <input class="form-check-input mt-0" wire:model="confEmail" type="checkbox" id="confEmail">
                                    <label for="confEmail" class=" col-form-label">¿Deseas recibir una confirmación
                                        por correo electrónico?</label>

                                </div>
                            </div>
                        </div>
                    </form>
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
</div>

@section('scripts')
<script>
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
{{-- @endsection --}}
