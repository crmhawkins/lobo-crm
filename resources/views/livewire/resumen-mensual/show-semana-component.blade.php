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

    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <input type="hidden" name="id" value="{{ csrf_token() }}">


        <div class="container-md ">

            @if ($day != null)
                <div>

                    <table class="table table-bordered border-dark" style="text-align: center">
                        <thead class="table-primary">
                            <tr>
                                <td colspan="2">Balance Eventos Semana {{ $index }}</td>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <tr>
                                <td>Total ingresos brutos</td>
                                <td>{{ $ingresosBrutos }}€</td>
                            </tr>
                            <tr class="table-success">
                                <td>Total liquido semanal</td>
                                <td>{{ $liquido }}€</td>
                            </tr>
                            <tr>
                                <td>Adelanto de los eventos</td>
                                <td>{{ $adelantoEventos }}€</td>
                            </tr>
                            <tr>
                                <td>Pendiente cobrar</td>
                                <td>{{ $pteCobrar }}€</td>
                            </tr>
                            <tr>
                                <td>Salario monitores</td>
                                <td>{{ $salarioMonitores }}€</td>
                            </tr>
                            <tr class="table-danger">
                                <td>Pte pago monitores</td>
                                <td>{{ $pteMonitores }}€</td>
                            </tr>
                            <tr>
                                <td>Desplazamiento/Gasolina</td>
                                <td>{{ $desplazamiento }}€</td>
                            </tr>
                            <tr>
                                <td>Seguros Sociales</td>
                                <td>X €</td>
                            </tr>
                            <tr>
                                <td>Material fungible</td>
                                <td>X €</td>
                            </tr>
                            <tr>
                                <td>Total gastos</td>
                                <td>{{ $gastos }}€</td>
                            </tr>
                            <tr>
                                <td class="table-primary">Rentabilidad</td>
                                @if ($rentabilidad > 0)
                                    <td class="table-succes">
                                    @else
                                    <td class="table-danger">
                                @endif
                                {{ $rentabilidad }}€</td>
                            </tr>
                            <tr class="table-warning">
                                <td>Otros gastos</td>
                                <td>X €</td>
                            </tr>
                            <tr>
                                <td>Compras</td>
                                <td>X €</td>
                            </tr>
                            <tr>
                                <td>Consumos eventos</td>
                                <td>X €</td>
                            </tr>
                            <tr>
                                <td>Alquiler furgonetas</td>
                                <td>X €</td>
                            </tr>
                            <tr>
                                <td>Pers Central + Seg.Soc. </td>
                                <td>{{ $gastosSemana['personalCentralYSegSoc'] }}€</td>
                            </tr>
                            <tr>
                                <td>Alarma</td>
                                <td>{{ $gastosSemana['alarma'] }}€</td>
                            </tr>
                            <tr>
                                <td>Seguro</td>
                                <td>{{ $gastosSemana['seguro'] }}€</td>
                            </tr>
                            <tr>
                                <td>Telefonia, internet, web</td>
                                <td>{{ $gastosSemana['telefonia'] }}€</td>
                            </tr>
                            <tr>
                                <td>Gestoria Fiscal</td>
                                <td>{{ $gastosSemana['gestoriaFiscal'] }}€</td>
                            </tr>
                            <tr>
                                <td>Gestoria laboral (Var)</td>
                                <td>{{ $gastosSemana['gestoriaLaboral'] }}€</td>
                            </tr>
                            <tr>
                                <td>Alquileres</td>
                                <td>{{ $gastosSemana['alquileres'] }}€</td>
                            </tr>
                            <tr>
                                <td>Bancos</td>
                                <td>{{ $gastosSemana['bancos'] }}€</td>
                            </tr>
                            <tr>
                                <td>Aberas Consultores</td>
                                <td>{{ $gastosSemana['personalCentralYSegSoc'] }}€</td>
                            </tr>
                            <tr>
                                <td>Informatico</td>
                                <td>{{ $gastosSemana['informatico'] }}€</td>
                            </tr>
                            <tr>
                                <td>Comunidad</td>
                                <td>{{ $gastosSemana['comunidad'] }}€</td>
                            </tr>
                            <tr>
                                <td>Suministros</td>
                                <td>{{ $gastosSemana['suministros'] }}€</td>
                            </tr>
                            <tr>
                                <td>Digmep</td>
                                <td>{{ $gastosSemana['digmep'] }}€</td>
                            </tr>
                            <tr>
                                <td>Carlos</td>
                                <td>{{ $gastosSemana['carlos'] }}€</td>
                            </tr>
                            <tr>
                                <td>Mybox</td>
                                <td>{{ $gastosSemana['mybox'] }}€</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-primary">
                            <tr>
                                <th>Bª Neto Semanal</th>
                                @if ($balance > 0)
                                    <th class="table-danger">
                                    @else
                                    <th>
                                @endif
                                {{ $balance }}€</th>
                            </tr>
                        </tfoot>
                    </table>


                </div>
            @endif


        </div>

        <br>
        <br>



    </form>


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
            @this.set('dia', $('#dia').val());
            @this.emit("loadDay");
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
