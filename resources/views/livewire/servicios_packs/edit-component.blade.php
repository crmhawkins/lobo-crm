@section('head')
    @vite(['resources/sass/productos.scss'])
@endsection
<div class="container mx-auto">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR PACK DE SERVICIO</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pack de Servicios</a></li>
                    <li class="breadcrumb-item active">Editar pack de servicio</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="update">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="container text-center">
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
                                <div class="col-sm-5">
                                    <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre"
                                        placeholder="Pack">
                                    @error('nombre')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="servicio" class="col-sm-2 col-form-label">Servicio </label>
                                <select class="col-sm-2 input-group-text" name="servicio" id="servicio" wire:model="servicio">
                                    <option class="dropdown-item" value="">Servicio</option>
                                    @foreach ($servicios as $i => $servicio)
                                        <option class="dropdown-item"  value="{{ $servicio->id }}">
                                            {{ $servicio->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="addServ" class="col-sm-1 btn btn-primary">Añadir</button>
                            </div>
                
                            @foreach ($serviciosPack as $key => $servicio)
                                <div class="mb-3 row d-flex align-items-center">
                                    <h4 for="servicio.{{ $key }}" class="col-sm-2 col-form-label">Servicios Relacionados</h4>
                                    <h4 for="servicio.{{ $key }}" class="col-sm-2 input-group-text">{{ $servicio->nombre }}
                                    </h4>
                                    <button type="button" wire:click="removeServ({{ $key }})" class="col-sm-1 btn btn-outline-danger">Eliminar</button>
                                </div>
                            @endforeach
                
                            <div class="container text-center">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <button type="submit" class="btn btn-outline-info">Guardar</button>
                                    </div>
                                    <div class="col">
                                        <button type="button" wire:click="destroy" class="btn btn-outline-danger">Eliminar</button>
                                    </div>
                
                                </div>
                            </div>
                
                
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    



</div>


@section('scripts')
    <script>
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
            dateFormat: 'dd/mm/yy',
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
                @this.set('fecha_nac', $('#datepicker').val());
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
