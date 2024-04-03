<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CAJA</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Caja</a></li>
                    <li class="breadcrumb-item active">Ver movimientos</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->


    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title" wire:key='rand()' >Ver movimientos de caja</h4>
                    @if (count($caja) > 0)
                        <table class="table-sm table-striped table-bordered mt-5"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;" >
                            <thead>
                                <tr>
                                    <th colspan="6">Saldo inicial</th>
                                    <th colspan="2">{{$saldo_inicial}}€</th>
                                </tr>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Concepto</th>
                                    <th scope="col">Asociado</th>
                                    <th scope="col">Desglose</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">(+)</th>
                                    <th scope="col">(-)</th>
                                    <th scope="col">Saldo</th>


                                    <th scope="col">Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($caja as $tipoIndex => $tipo)
                                    <tr>
                                        <td>{{ $tipo->fecha }}</td>
                                        <td>{{ $tipo->descripcion }}</td>
                                        @if ($tipo->pedido_id)
                                        <td>Factura Nº {{ $tipo->pedido_id }} - {{ $this->getCliente($tipo->pedido_id) }}</td>
                                        @elseif($tipo->poveedor_id)
                                        <td>{{ $this->proveedorNombre($tipo->poveedor_id )}}</td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td>{{$tipo->tipo_movimiento}}</td>
                                        <td>
                                            @if ($tipo->tipo_movimiento == 'Gasto')
                                                @switch($tipo->estado)
                                                    @case('Pendiente')
                                                    <span class="badge badge-warning">{{ $tipo->estado }}</span>
                                                        @break
                                                    @case("Pagado")
                                                    <span class="badge badge-success">{{ $tipo->estado }}</span>
                                                        @break
                                                    @case('Vencido')
                                                    <span class="badge badge-danger">{{ $tipo->estado }}</span>
                                                        @break
                                                    @default
                                                    <span class="badge badge-info">{{ $tipo->estado }}</span>
                                                @endswitch
                                            @endif
                                        </td>
                                        <td>
                                            @if ($tipo->tipo_movimiento == 'Ingreso')
                                                {{ $tipo->importe }} €
                                            @endif
                                        </td>
                                        <td>
                                            @if ($tipo->tipo_movimiento == 'Gasto')
                                                {{ $tipo->importe }} €
                                            @endif
                                        </td>

                                        <td>{{ $this->calcular_saldo($tipoIndex, $tipo->id) }}€</td>


                                        <td> <a href="caja-edit/{{ $tipo->id }}"
                                                class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Elige una semana</h5>
                    <div class="row">
                        <div class="col-12">
                            <input type="month" class="form-control" wire:model="mes" wire:change="cambioMes">
                        </div>
                    </div>
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" wire:click="Ingreso">Ingreso</button>
                        </div>
                        <div class="col-12">
                            <button class="w-100 btn btn-danger mb-2" wire:click="Gasto">Gasto</button>
                        </div>
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
