@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.css">
    @vite(['resources/sass/productos.scss'])
@endsection


@section('content')
    <div class="container mx-auto">
        <div class="d-flex justify-content-left align-items-center">
            <h1 class="me-5">Cursos</h1>

        </div>
        <h2>Todos los cursos</h2>
        <br>
        <a href="{{ route('cursos.create') }}" class="btn btn-warning">Añadir Curso</a>
        <br><br><br>

        @if (count($cursos) > 0)
            <table class="table" id="tableCursos">
                <thead>
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Denominación</th>
                        <th scope="col">Celebración</th>
                        <th scope="col">Fecha inicio</th>
                        <th scope="col">Fecha fin</th>

                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cursos as $curso)
                        <tr>
                            <td>{{ $curso->nombre }}</th>
                                @if ($curso->denominacion_id == 0)
                                    <td>Sin Denominación</td>
                                @else
                                    @foreach ($cursos_denominacion as $denominacion)
                                        @if ($denominacion->id == $curso->denominacion_id)
                                            <td>{{ $denominacion->nombre }}</td>
                                        @endif
                                    @endforeach
                                @endif
                                @if ($curso->celebracion_id == 0)
                                    <td>Sin Celebración</td>
                                @else
                                    @foreach ($cursos_celebracion as $celebracion)
                                        @if ($celebracion->id == $curso->celebracion_id)
                                            <td>{{ $celebracion->nombre }}</td>
                                        @endif
                                    @endforeach
                                @endif
                            <td>{{ $curso->fecha_inicio }}</th>
                            <td>{{ $curso->fecha_fin }}</td>



                            <td> <a href="cursos-edit/{{ $curso->id }}" class="btn btn-primary">Ver/Editar</a> </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif



    </div>

    </tbody>
    </table>

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
            console.log('entro');
            $('#tableCursos').DataTable({
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
    </script>
@endsection
@endsection
