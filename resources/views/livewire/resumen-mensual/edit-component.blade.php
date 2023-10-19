@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
    <style>
        h2 {
            display: inline-block;
            margin-right: 5%;

        }

        h3,
        .editar,
        .guardar {
            margin-right: 5%;

        }
    </style>
@endsection



<div class="container mx-auto">
    <h1>Mes {{ $month }}</h1>
    <h2>Resumen Mensual</h2>
    <br>
    <br>

    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <input type="hidden" name="id" value="{{ csrf_token() }}">


        <div class="container-md ">


            {{-- @if ($i_semana != null) --}}


            {{-- @if ($semana != null) --}}
            <div class="container-fluid">
                <div class="row">

                    {{-- {{ dd($semanas[$i_semana]) }} --}}
                    @foreach ($semanas as $i => $dia)
                        <div class="col-3">
                            {{-- {{ dd($semanas) }} --}}
                            @livewire('resumen-mensual.show-semana-component', ['index' => $i, 'day' => array_key_first($dia), 'gastosSemana' => $gastosSemanal], key($i))
                        </div>
                    @endforeach

                    <div class="col-4">
                        <table class="table table-bordered border-dark" style="text-align: center">
                            <thead class="table-bordered border-dark">
                                <tr class="table-warning">
                                    <th colspan="3">Gastos Mensuales</th>
                                </tr>
                                <tr>
                                    <th colspan="2">Semana del mes en curso</th>
                                    <th>{{ $currentWeek }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Personal Central + Seg.Soc.</td>
                                    <td> <input style="width: 60px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.personalCentralYSegSoc">€</td>
                                    <td>{{ $gastosSemanal['personalCentralYSegSoc'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Alarma</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.alarma">€</td>
                                    <td>{{ $gastosSemanal['alarma'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Seguro</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.seguro">€</td>
                                    <td>{{ $gastosSemanal['seguro'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Telefonia, internet, web</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.telefonia">€</td>
                                    <td>{{ $gastosSemanal['telefonia'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Gestoria Fiscal</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.gestoriaFiscal">€</td>
                                    <td>{{ $gastosSemanal['gestoriaFiscal'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Gestoria laboral (variable)</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.gestoriaLaboral">€</td>
                                    <td>{{ $gastosSemanal['gestoriaLaboral'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Alquileres</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.alquileres">€</td>
                                    <td>{{ $gastosSemanal['alquileres'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Bancos</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.bancos">€</td>
                                    <td>{{ $gastosSemanal['bancos'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Aberas Consultores</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.aberasConsultores">€</td>
                                    <td>{{ $gastosSemanal['aberasConsultores'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Informatico</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.informatico">€</td>
                                    <td>{{ $gastosSemanal['informatico'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Comunidad</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.comunidad">€</td>
                                    <td>{{ $gastosSemanal['comunidad'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Suministros</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.suministros">€</td>
                                    <td>{{ $gastosSemanal['suministros'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Digmep</td>
                                    <td> <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.digmep">€</td>
                                    <td>{{ $gastosSemanal['digmep'] }}€</td>
                                </tr>
                                <tr>
                                    <td>Carlos</td>
                                    <td>
                                        <input style="width: 40px; text-align:center" type="text"
                                            wire:model.defer="gastosMesArray.carlos">
                                        €
                                    </td>
                                    <td>{{ $gastosSemanal['carlos'] }}€</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1"><button type="button" class="btn btn-primary" wire:click="aplicarCambios">Aplicar
                                            cambios</button>
                                    </td>
                                    <td colspan="2"><button type="button" class="btn btn-danger" wire:click="back">Cancelar
                                        </button></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
                {{-- @endif --}}
                {{-- @endif --}}
            </div>
        </div>


        <br>
        <br>



    </form>


</div>





</div>

</tbody>
</table>
@section('scripts')
    {{-- <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script> --}}
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

        });
        console.log('select2')
        $('.form-control select').select2();


        $("#dia").datepicker();


        $("#dia").on('change', function(e) {
            @this.set('day', $('#dia').val());
            @this.emit("loadMonth");
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

        //observer para aplicar el datepicker de evento
        const observer = new MutationObserver((mutations, observer) => {
            console.log(mutations, observer);
        });
        observer.observe(document, {
            subtree: true,
            attributes: true
        });



        document.addEventListener('DOMSubtreeModified', (e) => {

        })
    </script>
@endsection
