<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">PRODUCTOS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active">Ver productos</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body row justify-content-center">
                    <div class="col-12">
                        <h4 class="mt-0 header-title font-24">Listado de todos los productos</h4>
                        <p class="sub-title../plugins mb-5">Listado completo de todos nuestros productos, para editar o
                            ver la informacion completa pulse el boton de Ver/Editar en la columna acciones.
                            @mobile
                                <br> Haz click en las imágenes para ver más información.
                            @endmobile
                        </p>
                    </div>
                    <div class="col-12 mb-5">
                        <a href="productos-create" class="btn btn-lg w-100 btn-primary">AÑADIR PRODUCTO</a>
                    </div>
                    @if (count($productos) > 0)
                        <div class="col-12">
                            <table id="datatable-buttons"
                                class="table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 90%; white-space: nowrap !important">
                                <thead>
                                    <tr>
                                        <th scope="col">Imagen</th>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Precio: </th>
                                        <th scope="col">Acciones: </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Recorre los pedidos --}}
                                    @foreach ($productos as $presup)
                                        <tr>
                                        <td @mobile @elsemobile width="10%"
                                            @endmobile><img class="img-fluid"
                                                src="{{ asset('storage/photos/' . $presup->foto_ruta) }}"
                                                alt="Card image cap"></td>
                                        <td>{{ $presup->nombre }}</td>
                                        <td> &nbsp; {{ $presup->precio }} €</td>
                                        <td> &nbsp; <a href="productos-edit/{{ $presup->id }}"
                                                class="btn btn-primary">Ver/Editar</a> </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <h6 class="text-center">No se encuentran productos disponibles</h6>
                @endif

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

</div>
