<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CALENDARIO</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0);">Calendario</a></li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->

    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <style>
                        .tooltip-inner {
                            text-align: left !important;
                        }
                    </style>
                    {{-- <h4 class="mt-0 header-title">Listado de todos los articulos</h4>
                    <p class="sub-title../plugins">Listado completo de todos nuestros articulos, para editar o ver la informacion completa pulse el boton de Editar en la columna acciones.
                    </p> --}}


                    <div id='calendar' class="w-100"></div>
                    <button class="btn btn-secondary w-100 mb-4 mt-4" onclick="savePDF()"
                        id="export-pdf-button">Exportar a PDF</button>

                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div>


@section('scripts')
    @php use Carbon\Carbon; @endphp
    <!-- Required datatable js -->
    {{-- <script src="../assets/js/jquery.min.js"></script> --}}
    <script src="../assets/js/jquery.slimscroll.js"></script>

    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="../plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="../plugins/datatables/buttons.bootstrap4.min.js"></script>
    <script src="../plugins/datatables/jszip.min.js"></script>
    <script src="../plugins/datatables/pdfmake.min.js"></script>
    <script src="../plugins/datatables/vfs_fonts.js"></script>
    <script src="../plugins/datatables/buttons.html5.min.js"></script>
    <script src="../plugins/datatables/buttons.print.min.js"></script>
    <script src="../plugins/datatables/buttons.colVis.min.js"></script>
    <!-- Responsive examples -->
    <script src="../plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="../assets/pages/datatables.init.js"></script>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://superal.github.io/canvas2image/canvas2image.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: "es",
                headerToolbar: {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' // user can switch between the two
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                    list: 'Lista'
                },
                events: [
                    @foreach ($eventos as $evento)
                        @if ($presupuestos->where('id_evento', $evento->id)->first()->estado != 'Cancelado')
                            {
                                title: '{{ $this->categorias->where('id', $evento->eventoNombre)->first()->nombre }} ',
                                start: '{{ $evento->diaEvento }}',
                                end: '{{ $evento->diaFinal }}',
                                description: '<br> <b>Protagonista:</b> {{ $evento->eventoProtagonista }} <br> <b>Niños:</b> {{ $evento->eventoNiños }}<br> <b>Adultos:</b> {{ $evento->eventoAdulto }}',
                                id: '{{ $evento->id }}',
                                html: true,
                                @if ($presupuestos->where('id_evento', $evento->id)->first()->estado == 'Pendiente')
                                    color: '#f39200',
                                    presupuestoId: '{{ $presupuestos->where('id_evento', $evento->id)->first()->id }}',
                                @elseif ($presupuestos->where('id_evento', $evento->id)->first()->estado == 'Aceptado')
                                    color: '#30419b',
                                        presupuestoId:
                                        '{{ $presupuestos->where('id_evento', $evento->id)->first()->id }}',
                                @elseif ($presupuestos->where('id_evento', $evento->id)->first()->estado == 'Completado')
                                    color: '#009e4e',
                                        presupuestoId:
                                        '{{ $presupuestos->where('id_evento', $evento->id)->first()->id }}',
                                @elseif ($presupuestos->where('id_evento', $evento->id)->first()->estado == 'Facturado')
                                    color: '#991b7a',
                                        presupuestoId:
                                        '{{ $presupuestos->where('id_evento', $evento->id)->first()->id }}',
                                @endif
                            },
                        @endif
                    @endforeach
                ],
                eventClick: function(info) {
                    if (info.event.extendedProps.presupuestoId != undefined) {
                        console.log('hola');
                        window.open('https://crm.fabricandoeventosjerez.com/admin/presupuestos-edit/' +
                            info.event.extendedProps.presupuestoId);
                    }
                },
                eventDidMount: function(info) {
                    var tooltip = new bootstrap.Tooltip(info.el, {
                        title: '<h5>' + info.event.title + '</h5>' + info.event.extendedProps
                            .description,
                        placement: 'top',
                        trigger: 'hover',
                        html: true
                    });
                },
            });
            calendar.render();



        });

        function savePDF() {

            html2canvas($('#calendar')[0]).then(function(canvas) {
                var doc = new jsPDF();
                var img = Canvas2Image.convertToPNG(canvas);
                doc.setProperties({
                    title: 'Eventos',
                    subject: 'Info about PDF',
                    author: 'PDFAuthor',
                    keywords: 'generated, javascript, web 2.0, ajax',
                    creator: 'My Company'
                });
                doc.setFontSize(22);
                doc.text(15, 20, 'Eventos');
                doc.addImage(img, 'PNG', 15, 40, 180, 160);
                doc.save('div.pdf');
                //   $(".response").append(canvas);
            })

            // var calendarEl = $('#calendar');
            // console.log(calendarEl[0].innerHTML)
            // // Crea un nuevo objeto jsPDF
            // var doc = new jsPDF();

            // // Agrega el contenido HTML del calendario al documento PDF
            // // doc.addHTML(calendarEl, 10, 10);
            // // doc.addHTML(calendarEl, function () {
            // //     doc.save('calendario.pdf');
            // // });
            // doc.fromHTML(`<html><head><title>Eventos</title></head><body>` + calendarEl[0].innerHTML + `</body></html>`);
            // doc.save('div.pdf');

            // // Guarda el archivo PDF
            // doc.save('calendario.pdf');
        }
    </script>
@endsection
