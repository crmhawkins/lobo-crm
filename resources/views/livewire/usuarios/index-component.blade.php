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
@endsection
