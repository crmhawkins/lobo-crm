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


    <div class="row">
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
                                    <th scope="col">Presupuesto</th>
                                    <th scope="col">Desglose</th>
                                    <th scope="col">Debe (+)</th>
                                    <th scope="col">Haber (-)</th>
                                    <th scope="col">Saldo</th>


                                    <th scope="col">Ver</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($caja as $tipoIndex => $tipo)
                                    <tr>
                                        <td>{{ $tipo->fecha }}</td>
                                        <td>{{ $tipo->descripcion }}</td>
                                        <td>{{ $tipo->presupuesto_id }}</td>
                                        <td>{{$tipo->tipo_movimiento}}</td>
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
            <div class="card m-b-30 position-fixed">
                <div class="card-body">
                    <h5>Elige un año</h5>
                    <div class="row">
                        <div class="col-12">
                            <input type="number" class="form-control" wire:model="semana"
                                wire:change="cambioSemana">
                        </div>
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
