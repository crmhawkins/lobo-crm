
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">MERCADERÍA</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Mercadería</a></li>
                    <li class="breadcrumb-item active">Ver órdenes de compra de mercadería</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->


    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todas las órdenes de compra de mercadería</h4>
                    @if (count($ordenes_mercaderias) > 0)
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Nº de órden</th>
                                    <th scope="col">Fecha de emisión</th>
                                    <th scope="col">Precio total</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordenes_mercaderias as $mercaderia)
                                    <tr>
                                        <td>{{ $mercaderia->numero }}</td>
                                        <td>{{ $mercaderia->fecha }}</td>
                                        <td>{{ $mercaderia->precio }}</td>
                                        <td> <a href="orden-mercaderia-edit/{{ $mercaderia->id }}" class="btn btn-primary">Ver/Editar</a> </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script src="../assets/js/jquery.slimscroll.js"></script>

    {{-- <script src="../plugins/datatables/jquery.dataTables.min.js"></script> --}}
    <script src="../plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    {{-- <script src="../plugins/datatables/dataTables.buttons.min.js"></script> --}}
    <script src="../plugins/datatables/buttons.bootstrap4.min.js"></script>
    {{-- <script src="../plugins/datatables/jszip.min.js"></script> --}}
    {{-- <script src="../plugins/datatables/pdfmake.min.js"></script> --}}
     {{-- <script src="../plugins/datatables/vfs_fonts.js"></script> --}}
    {{-- <script src="../plugins/datatables/buttons.html5.min.js"></script> --}}
    <script src="../plugins/datatables/buttons.colVis.min.js"></script>
    <!-- Responsive examples -->
    <script src="../plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="../assets/pages/datatables.init.js"></script>
    <!-- test examples -->
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

@endsection
