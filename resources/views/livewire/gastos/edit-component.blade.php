<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR GASTO {{ $identificador }}</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Gastos</a></li>
                    <li class="breadcrumb-item active">Editar Gasto</li>
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
                            <div class="col-md-6">
                                <label for="tipo_gasto" class="col-sm-12 col-form-label">Tipo de gasto</label>
                                <div class="col-sm-12" wire:ignore>
                                    <select class="form-control js-example-responsive" wire:model="tipo_gasto">
                                        <option value="{{null}}">-- SELECCIONA UN TIPO DE GASTO --</option>
                                        @foreach($tipos_gasto as $tipo_gasto)
                                        <option value="{{$tipo_gasto->id}}">{{$tipo_gasto->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('tipo_gasto')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="nombre_gasto" class="col-sm-12 col-form-label">Concepto del gasto</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="nombre_gasto" class="form-control" placeholder="...">
                                    @error('nombre_gasto')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="cuantia" class="col-sm-12 col-form-label">Cuantía del gasto</label>
                                <div class="col-sm-12">
                                    <input type="number" wire:model.defer="cuantia" class="form-control" placeholder="...">
                                    @error('cuantia')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="date" class="col-sm-12 col-form-label">Fecha de emisión del gasto</label>
                                <div class="col-sm-12">
                                    <input type="date" wire:model.defer="date" class="form-control" placeholder="15/02/2023" id="datepicker">
                                    @error('date')
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
                    <h5>Opciones de guardado</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                factura</button>
                            <button class="w-100 btn btn-danger mb-2" id="alertaEliminar">Borrar
                                factura</button>
                        </div>
                    </div>
                </div>
            </div>
            @if($gastos->tipo_gasto == "Fijo")
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            @if($activo == 1)
                            <button class="w-100 btn btn-warning mb-2" id="alertaDesactivar">Desactivar repetición del gasto</button>
                            @else
                            <button class="w-100 btn btn-warning mb-2" id="alertaActivar">Activar repetición del gasto</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @section('scripts')
    <script>
        $("#alertaGuardar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('update');
                }
            });
        });

        $("#alertaEliminar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'error',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('confirmDelete');
                }
            });
        });

        $("#alertaActivar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('activarGasto');
                }
            });
        });

        $("#alertaDesactivar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'error',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('desactivarGasto');
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
            $('.js-example-responsive').select2({
                placeholder: "-- Seleccione un tipo de gasto --",
                width: 'resolve'
            }).on('change', function() {
                var selectedValue = $(this).val();
                // Llamamos a la función listarPresupuesto() pasando el valor seleccionado
                @this.set('tipo_gasto', selectedValue);
            });


            console.log('select2')
            $("#datepicker").datepicker();

            $("#datepicker").on('change', function(e) {
                @this.set('fecha_emision', $('#datepicker').val());
            });
            $("#datepicker2").datepicker();

            $("#datepicker2").on('change', function(e) {
                @this.set('fecha_vencimiento', $('#datepicker2').val());
            });


        });
    </script>
    {{-- SCRIPT PARA SELECT 2 CON LIVEWIRE --}}
    <script>
        window.initSelect2 = () => {
            jQuery("#id_presupuesto").select2({
                minimumResultsForSearch: 2,
                allowClear: false
            });
        }

        initSelect2();
        window.livewire.on('select2', () => {
            initSelect2();
        });
    </script>
    @endsection