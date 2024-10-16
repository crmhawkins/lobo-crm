@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">PRODUCTOS MARKETING</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos-Marketing</a></li>
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
                        <h4 class="mt-0 header-title font-24">Listado de todos los productos marketing</h4>
                        <p class="sub-title../plugins mb-5">Listado completo de todos nuestros productos, para editar o
                            ver la informacion completa pulse el boton de Ver/Editar en la columna acciones.
                            @mobile
                                <br> Haz click en las imágenes para ver más información.
                            @endmobile
                        </p>
                    </div>
                    <div class="col-12 mb-5">
                        <a href="productosmarketing-create" class="btn btn-lg w-100 btn-primary">AÑADIR PRODUCTO</a>
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
                                        <th scope="col">Tipo de precio</th>
                                        <th scope="col">Acciones: </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Recorre los pedidos --}}
                                    @foreach ($productos as $presup)
                                        <tr>
                                        <td @mobile @elsemobile width="10%"
                                            @endmobile class="max-height: 100px;"><img class="img-fluid"
                                                src="{{ asset('storage/' . $presup->foto_ruta) }}"
                                                alt="Card image cap"></td>
                                        <td>{{ $presup->nombre }}</td>
                                        <td> &nbsp; {{ $tipoPrecioMap[$presup->tipo_precio] ?? 'Precio no definido' }}</td>
                                        <td> &nbsp; 
                                            @if($canEdit)
                                                <a href="productos-marketing-edit/{{ $presup->id }}" class="btn btn-primary">Ver/Editar</a> 
                                            @else
                                                <a href="productos-marketing-edit/{{ $presup->id }}" class="btn btn-primary">Ver</a>
                                            @endif
                                        </td>
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
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
    <!-- Responsive examples -->
    <script src="../assets/pages/datatables.init.js"></script>
    @endsection

</div>
