<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">GESTIÓN DE USUARIOS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Usuarios</a></li>
                    <li class="breadcrumb-item active">Gestión de usuarios</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title font-24">Gestión de usuarios</h4>
                    <p class="sub-title../plugins mb-1">Listado completo de los usuarios, disponible para todos los
                        usuarios con rol de administrador para
                        el manejo, cambio o desactivación de usuarios.
                    </p>
                    @if (count($usuarios) > 0)
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Rol</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Almacen</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $this->mostrarRol($user->role) }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->almacen_id == 0)
                                                Sin almacén
                                            @else
                                                {{ $user->almacen->almacen ?? 'Almacén no encontrado' }}
                                            @endif
                                        </td>
                                        <td> <a href="usuarios-edit/{{ $user->id }}"
                                                class="btn btn-primary">Ver/Editar</a> </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <table class="table table-striped table-bordered dt-responsive nowrap mt-3">
                            <tr>
                                <td colspan="5"><a href="usuarios-create"
                                        class="btn btn-lg btn-primary w-100">AÑADIR NUEVO USUARIO</a>
                                </td>
                            </tr>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
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
