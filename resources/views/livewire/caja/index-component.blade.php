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
                <div class="table-responsive card-body">
                    <h4 class="mt-0 header-title" wire:key='rand()'>Ver movimientos de caja</h4>
                    @if (count($caja) > 0)
                        <table class="table-sm table-striped table-bordered mt-5"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;" >
                            <thead>
                                <tr>
                                    <th colspan="9">Saldo inicial</th>
                                    <th colspan="3">{{$saldo_inicial}}€</th>
                                </tr>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Concepto</th>
                                    <th scope="col">Asociado</th>
                                    <th scope="col">Desglose</th>
                                    <!--<th scope="col">Estado</th> -->
                                    <th scope="col">Importe</th>
                                    <th scope="col">% Iva</th>
                                    <th scope="col">Retencion</th>
                                    <th scope="col">Descuento</th>
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
                                        @if (isset($tipo->pedido_id))
                                        <td>{{ $this->getFactura($tipo->pedido_id) }}</td>
                                        @elseif($tipo->poveedor_id)
                                        <td>{{ $this->proveedorNombre($tipo->poveedor_id )}}</td>
                                        @else
                                        <td></td>
                                        @endif
                                        <td>{{$tipo->tipo_movimiento}}</td>
                                        <!-- <td>
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
                                        </td> -->
                                        <td>{{ $tipo->importe }} €</td>
                                        @if($tipo->tipo_movimiento == 'Gasto')
                                            <td>{{ floatval($tipo->iva) }}%</td>
                                            <td>{{ floatval($tipo->retencion) }}%</td>
                                            <td>{{ floatval($tipo->descuento) }}%</td>
                                        @else
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        @endif
                                        <td>
                                            @if ($tipo->tipo_movimiento == 'Ingreso')
                                                {{ $tipo->importe }} €
                                            @endif
                                        </td>
                                        <td>
                                            @if ($tipo->tipo_movimiento == 'Gasto')
                                                {{ floatval($tipo->total) }} €
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
                    <h5>Elige un mes</h5>
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
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
<script src="../assets/pages/datatables.init.js"></script>

    @endsection
