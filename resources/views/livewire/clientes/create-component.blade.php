{{-- {{ var_dump($eventoServicios) }} --}}
{{-- @section('content') --}}
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
                    <form wire:submit.prevent="submit">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ csrf_token() }}">
                        <div class="form-group row">
                            <br>
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Tipo de cliente</h5>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-sm-1 d-inline-flex align-items-center">
                                        <input class="form-check-input mt-0" wire:model="tipo_cliente" type="radio"
                                            value="1" id="check1">
                                        <label for="check1" class=" col-form-label">Empresa</label>
                                    </div>
                                    <div class="form-group col-sm-11">
                                        &nbsp;
                                    </div>
                                    <div class="col-sm-1 d-inline-flex align-items-center">
                                        <input class="form-check-input mt-0" wire:model="tipo_cliente" type="radio"
                                            value="0" id="check2">
                                        <label for="check2" class=" col-form-label">Particular</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos del cliente</h5>
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
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">DNI/CIF</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="dni_cif" class="form-control" name="dni_cif"
                                        id="dni_cif" placeholder="DNI o CIF">
                                    @error('dni_cif')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .dni_cif {
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
                            <div class="form-group col-md-1">
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
                            <div class="form-group col-sm-1">
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
                                    <input type="text" wire:model="telefono" class="form-control" name="telefono"
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
                            <div class="form-group col-sm-1">
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
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-2">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Precio Cremas</label>
                                <div class="col-sm-12">
                                    <input type="number" step=".01" wire:model="precio_crema" class="form-control" name="precio_crema"
                                        id="precio_crema" placeholder="8.34">
                                    @error('precio_crema')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .precio_crema {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-2">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Precio Vodka 0,7L</label>
                                <div class="col-sm-12">
                                    <input type="number" step=".01" wire:model="precio_vodka07l" class="form-control" name="precio_vodka07l"
                                        id="precio_vodka07l" placeholder="23.50">
                                    @error('precio_vodka07l')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .precio_vodka07l {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-2">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Precio Vodka 1,75L</label>
                                <div class="col-sm-12">
                                    <input type="number" step=".01" wire:model="precio_vodka175l" class="form-control" name="precio_vodka175l"
                                        id="precio_vodka175l" placeholder="52.00">
                                    @error('precio_vodka175l')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .precio_vodka175l {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-2">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Precio Vodka 3L</label>
                                <div class="col-sm-12">
                                    <input type="number" step=".01" wire:model="precio_vodka3l" class="form-control" name="precio_vodka3l"
                                        id="precio_vodka3l" placeholder="135.00">
                                    @error('precio_vodka3l')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .precio_vodka3l {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="forma_pago_pref" class="col-sm-12 col-form-label">Forma de pago preferida</label>
                                <div class="col-sm-12">
                                    <select wire:model="forma_pago_pref" class="form-control" name="forma_pago_pref" id="forma_pago_pref">
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="giro_bancario">Giro Bancario</option>
                                        <option value="pagare">Pagare</option>
                                        <option value="confirming">Confirming</option>
                                        <option value="30/45/60">30/45/60</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                    @error('forma_pago_pref')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .forma_pago_pref {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nota</label>
                                <div class="col-sm-12">
                                    <input type="textarea" wire:model="nota" class="form-control"
                                        name="nota" id="nota" placeholder="Nota">
                                    @error('nota')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .nota {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>

                </div>
                </form>
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
