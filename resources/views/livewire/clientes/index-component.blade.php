<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CLIENTES</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Clientes</a></li>
                    <li class="breadcrumb-item active">Todos los clientes</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">

                    <h4 class="mt-0 header-title">Listado de todos los clientes</h4>
                    <p class="sub-title../plugins">Listado completo de todos nuestros clientes, para editar o ver la
                        informacion completa pulse el boton de Editar en la columna acciones.
                    </p>
                    <div class="col-12 mb-5">
                        <a href="clientes-create" class="btn btn-lg w-100 btn-primary">AÑADIR CLIENTE</a>
                    </div>
                    @if ($clientes != null)
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">NIF/DNI</th>
                                    <th scope="col">Teléfono</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Nota</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientes as $cliente)
                                    <tr>
                                        <td>{{ $cliente->nombre }}</td>
                                        <td>{{ $cliente->dni_cif }}</td>
                                        <td>{{ $cliente->telefono }}</td>
                                        <td>{{ $cliente->email }}</td>
                                        <td style="max-width: 200px; overflow:hidden;">{{ substr($cliente->nota, 0, 50)}}</td>
                                        <td>@if(($cliente->estado) == "1")
                                            <span class="badge badge-warning">Pendiente</span>
                                            @elseif(($cliente->estado) == "2")
                                            <span class="badge badge-success">Aceptado</span>
                                            @elseif(($cliente->estado) == "3")
                                            <span class="badge badge-danger">Rechazado</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->role != 3 && Auth::user()->role != 2)
                                            <td><a href="clientes-edit/{{ $cliente->id }}"class="btn btn-primary">Ver/Editar</a> </td>
                                        @else
                                            <td><a href="clientes-edit/{{ $cliente->id }}"class="btn btn-primary">Ver</a> </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                    @endif
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
</div>


@section('scripts')
<script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
<script src="../assets/pages/datatables.init.js"></script>
@endsection
