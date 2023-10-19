<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CREAR MONITOR</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Monitores</a></li>
                    <li class="breadcrumb-item active">Crear Monitor</li>
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

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model="nombre" class="form-control" name="nombre"
                                        id="nombre" placeholder="José Carlos...">
                                    @error('nombre')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Apellidos</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model="apellidos" class="form-control" name="apellidos"
                                        id="apellidos" placeholder="Pérez...">
                                    @error('apellidos')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Alias del
                                    monitor</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="alias" class="form-control" name="alias"
                                        placeholder="Alias del monitor">
                                    @error('alias')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Fecha de
                                    nacimiento</label>
                                <div class="col-sm-11">
                                    <input type="date" wire:model="fechaNa" class="form-control" name="fechaNa"
                                        id="datepicker" placeholder="15/02/2023">
                                    @error('fechaNa')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">DNI</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="dni" class="form-control" name="dni"
                                        id="Documento de identidad">
                                    @error('fechaNa')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Número de la Seguridad
                                    Social</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="num_ss" class="form-control" name="num_ss"
                                    placeholder="Número de la Seguridad Social">
                                    @error('fechaNa')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-5">

                                <label for="example-text-input" class="col-sm-12 col-form-label">Domicilio</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="domicilio" class="form-control" name="domicilio"
                                        id="domicilio" placeholder="domicilio">
                                    @error('localidad')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-4">

                                <label for="example-text-input" class="col-sm-12 col-form-label">Provincia</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="provincia" class="form-control"
                                        name="provincia" id="provincia" placeholder="provincia">
                                    @error('localidad')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Localidad</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="localidad" class="form-control"
                                        name="localidad" id="localidad" placeholder="Localidad">
                                    @error('localidad')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Código
                                    postal</label>
                                <div class="col-sm-11">
                                    <input type="number" wire:model="codigo_postal" class="form-control"
                                        name="codigo_postal" id="codigo_postal" placeholder="123456...">
                                    @error('codigo_postal')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Email</label>
                                <div class="col-sm-11">
                                    <input type="email" wire:model="email" class="form-control" name="email"
                                        id="email" placeholder="123456...">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Telefono</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="telefono" class="form-control" name="telefono"
                                        id="telefono" placeholder="123456...">
                                    @error('telefono')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nivel de
                                    estudios</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="nivel_estudios" class="form-control"
                                        name="nivel_estudios" id="nivel_estudios" placeholder="123456...">
                                    @error('codigo_postal')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre del
                                    padre</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="nombre_padre" class="form-control"
                                        name="nombre_padre" id="nombre_padre" placeholder="123456...">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre de la
                                    madre</label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="nombre_madre" class="form-control"
                                        name="nombre_madre" id="nombre_madre" placeholder="123456...">
                                    @error('nombre_madre')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
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
                text: 'Pulsa el botón de confirmar para guardar el monitor.',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('submit');
                }
            });
        });
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                'Octubre', 'Noviembre', 'Diciembre'
            ],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        document.addEventListener('livewire:load', function() {


        })
        $(document).ready(function() {
            console.log('select2')
            $("#datepicker").datepicker();

            $("#datepicker").on('change', function(e) {
                @this.set('fechaNa', $('#datepicker').val());
            });

        });

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.className = "fas fa-eye-slash";
            } else {
                passwordInput.type = "password";
                eyeIcon.className = "fas fa-eye";
            }
        }
    </script>
@endsection
