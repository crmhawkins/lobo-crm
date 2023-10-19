@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.css">
    @vite(['resources/sass/productos.scss'])
@endsection



{{-- {{ var_dump($dia) }} --}}
{{ var_dump($dia) }}
{{-- {{ var_dump($selectedEventos) }} --}}
{{ var_dump($dia != '') }}
{{-- {{ var_dump($programasDia) }} --}}
{{-- {{ var_dump($selectedEventos !== null ) }}
    {{ var_dump(count($selectedEventos) > 0 )  }} --}}
@section('content')
    <div class="container mx-auto">
        <div class="d-flex justify-content-left align-items-center">
            <h1 class="me-5">Agenda</h1>

        </div>
        <h2>Eventos de hoy</h2>
        <br>
        <a href="{{ route('programas.create') }}" class="btn btn-warning">Añadir Programa</a>
        <br><br><br>

        <form wire:submit.prevent="submit">
            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
            <input type="hidden" name="id" value="{{ csrf_token() }}">

            <div class="mb-3 row d-flex align-items-center">
                <label for="dia" class="col-sm-2 col-form-label">Selecciona un día</label>
                <div class="col-sm-10">
                    <input type="text" name="dia" class="form-control" placeholder="15/02/2023" id="dia">
                    @error('dia')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="button" wire:click="loadDay">Mirar dia</button>
            </div>




            {{-- {{ dd($dia === null) }} --}}
            {{ var_dump($dia !== null) }}
            @if ($dia != null)

                <div>
                    <h2>Dia {{ $dia }}</h2>

                    @if ($resumenDia != null)
                        @foreach ($resumenDia as $indice => $evento)
                            <div class="input-group mb-3">

                                <span class="input-group-text" id="basic-addon1">#{{ $evento['id'] }}</span>
                                <span class="input-group-text" id="basic-addon1">Evento:
                                    {{ $evento['eventoNombre'] }}</span>
                                <span class="input-group-text" id="basic-addon1">Tlf:
                                    {{ $evento['eventoTelefono'] }}</span>
                                <span class="input-group-text" id="basic-addon1">Contato:
                                    {{ $evento['eventoContacto'] }}</span>
                                <span class="input-group-text" id="basic-addon1">Montaje:
                                    {{ $evento['eventoMontaje'] }}</span>

                            </div>
                            <div class="input-group mb-3">
                                {{-- TODO Implementar adelanto en el presupuesto --}}
                                <span class="input-group-text" id="basic-addon1">Adelanto:</span>
                                <span class="input-group-text" id="basic-addon1">Pendiente:
                                    {{ $evento['presupuesto']['precioFinal'] }}</span>
                                <span class="input-group-text" id="basic-addon1">Nº Niños:
                                    {{ $evento['eventoNiños'] }}</span>
                                <span class="input-group-text" id="basic-addon1">Lugar:
                                    {{ $evento['eventoLugar'] }}</span>
                                <span class="input-group-text" id="basic-addon1">Localidad:
                                    {{ $evento['eventoLocalidad'] }}</span>

                            </div>

                            @if (count($evento['servicios']) > 0)
                                <div>
                                    <table class="table table-striped" style="text-align: center">
                                        <thead class="table-primary">
                                            <th>Montaje</th>
                                            <th>Servicio</th>
                                            <th>Material</th>
                                            <th>Hora de montaje</th>
                                            <th>Hora comienzo</th>
                                            <th>Hora fin</th>
                                            <th>Tiempo desmontaje</th>
                                            <th>Monitor</th>
                                            <th>Sueldo</th>
                                            <th>Desplazamiento</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($evento['servicios'] as $servicio)
                                                @foreach ($servicio['programas'] as $index => $programa)
                                                    <tr class="table-secondary">
                                                        {{-- Todo añadir unidades de tiempo al tiempo montaje --}}
                                                        <td>{{ $servicio['tiempoMontaje'] }} Min</td>
                                                        @if ($index == 0)
                                                            <td>{{ $servicio['servicio']['nombre'] }}</td>
                                                            {{-- Todo Inventario --}}
                                                            <td>Material</td>
                                                        @else
                                                            <td></td>
                                                            <td></td>
                                                        @endif
                                                        <td>{{ $programa['comienzoMontaje'] }}</td>
                                                        <td>{{ $programa['comienzoEvento'] }}</td>
                                                        <td>{{ $programa['horas'] }}</td>
                                                        <td>{{ $programa['tiempoDesmontaje'] }}</td>
                                                        <td>{{ $programa['monitor']['nombre'] }}
                                                            {{ $programa['monitor']['apellidos'] }}</td>
                                                        <td>{{ $programa['precioMonitor'] }}</td>
                                                        <td>{{ $programa['costoDesplazamiento'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Adelanto</th>
                                                <th>Pte Cobro</th>
                                                <th>Ser. Social</th>
                                                <th>Material</th>
                                                <th style="background-color: rgb( 219, 198, 94 )" colspan="2">IVA</th>
                                                <th style="background-color: rgb( 219, 198, 94 )">Total</th>
                                                <th style="background-color: rgb( 224, 98, 70 )">Total Monitores</th>
                                                <th style="background-color: rgb( 224, 98, 70 )">Pagado</th>
                                                <th style="background-color: rgb( 224, 98, 70 )">Pendiente</th>
                                            </tr>
                                            <tr>
                                                {{-- Todo Adelanto en el presupuesto --}}
                                                <td>Adelanto</td>
                                                <td>{{ $evento['presupuesto']['precioFinal'] }} - Adelanto</td>
                                                <td>Calculo seguridad social</td>
                                                {{-- Todo Inventario --}}
                                                <td>Material</td>
                                                {{-- Todo iva --}}
                                                <td class="table-warning">Si/No</td>
                                                <td class="table-warning">X%</td>
                                                <td class="table-warning"></td>

                                                <td class="table-danger"> {{ $this->getTotalMonitores($indice) }}</td>

                                                <td class="table-danger">{{ $this->getTotalMonitoresPagado($indice) }}
                                                </td>

                                                <td class="table-danger">
                                                    {{ $this->getTotalMonitores($indice) - $this->getTotalMonitoresPagado($indice) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <br>
                                <table class="table table-sm" style="text-align: center">
                                    <thead>
                                        <tr class="table-primary">
                                            <th colspan="4" style="background-color: rgb(   26, 97, 178  )">Balance
                                                Final
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="background-color: rgb(  34, 178, 26 )">Ingresos</th>
                                            <th style="background-color: rgb( 224, 98, 70 )">Gastos</th>
                                            <th style="background-color: rgb(   26, 128, 178  )">Balance</th>
                                            <th style="background-color: rgb(  34, 178, 26 )">Pagado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="table-success">
                                                {{ $evento['presupuesto']['precioFinal'] }}
                                            </td>
                                            {{-- Todo añadir SS y material --}}
                                            <td class="table-danger">
                                                {{ $this->getGastos($indice) }}
                                            </td>
                                            <td class="table-primary">
                                                {{ $this->getBalance($indice) }}
                                            </td>
                                            <td class="table-success">
                                                Si/No
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        @endforeach
                    @endif
                </div>


            @endif
        </form>
    </div>


@section('scripts')
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#dia").datepicker();

            $("#dia").on('change', function(e) {
                @this.set('dia', $('#dia').val());
            });
            $('#tableEmpresas').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                buttons: [{
                    extend: 'collection',
                    text: 'Export',
                    buttons: [{
                            extend: 'pdf',
                            className: 'btn-export'
                        },
                        {
                            extend: 'excel',
                            className: 'btn-export'
                        }
                    ],
                    className: 'btn btn-info text-white'
                }],
                "language": {
                    "lengthMenu": "Mostrando _MENU_ registros por página",
                    "zeroRecords": "Nothing found - sorry",
                    "info": "Mostrando página _PAGE_ of _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ total registros)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "zeroRecords": "No se encontraron registros coincidentes",
                }
            });

            addEventListener("resize", (event) => {
                location.reload();
            })
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
    </script>
@endsection
@endsection
