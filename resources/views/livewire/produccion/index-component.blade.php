<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">PRODUCCIÓN</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Producción</a></li>
                    <li class="breadcrumb-item active">Ver órdenes de producción</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->


    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todas las órdenes de producción</h4>
                    <a href="{{ route('produccion.create') }}" class="btn btn-lg btn-primary w-100 mb-1">NUEVA ÓRDEN DE
                        PRODUCCIÓN</a>
                    <a style="margin-bottom: 15px;" href="{{ route('materiales-producto.index') }}" class="btn btn-lg btn-primary w-100">ASIGNACIÓN DE
                        MATERIALES A PRODUCTOS</a>
                    @if (count($ordenes_produccion) > 0)

                    <!-- Aquí comienza el botón desplegable para filtrar por columna -->
                     <div id="Botonesfiltros">
                        <div class="dropdown ">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             Filtrar por Columna
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-column="0">Nº de órden</a>
                                <a class="dropdown-item" href="#" data-column="1">Fecha de emisión</a>
                                <a class="dropdown-item" href="#" data-column="2">Almacén</a>
                                <a class="dropdown-item" href="#" data-column="3">Estado</a>
                                <!-- Agrega más ítems según las columnas de tu tabla -->
                            </div>
                            <!-- Aquí termina el botón desplegable -->
                            <button class="btn btn-primary ml-2" id="clear-filter">Eliminar Filtro</button>
                        </div>
                     </div>

                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Nº de órden</th>
                                    <th scope="col">Fecha de emisión</th>
                                    <th scope="col">Almacén</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ordenes_produccion as $produccion)
                                    <tr>
                                        <td>{{ $produccion->numero }}</td>
                                        <td>{{ $produccion->fecha }}</td>
                                        <td>{{ $this->getAlmacen($produccion->almacen_id) }}</td>
                                        <td>
                                            @if(($produccion->estado) == "0")
                                            <span class="badge badge-warning">Pendiente</span>
                                            @elseif(($produccion->estado) == "1")
                                            <span class="badge badge-success">Completado</span>
                                            @endif
                                        </td>

                                        <td> <a href="produccion-edit/{{ $produccion->id }}"
                                                class="btn btn-primary">Ver</a>
                                            </td>
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
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
<script src="../assets/pages/datatables.init.js"></script>

@endsection
