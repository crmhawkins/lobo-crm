<div class="container-fluid" >
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CUADRO DE FLUJO</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Caja</a></li>
                    <li class="breadcrumb-item active">Cuadro de flujo</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->


    <div class="row" style="align-items: start !important">
        
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="table-responsive card-body">
                    
                    <div wire:loading.flex class="loader-overlay">
                        <div class="spinner"></div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label for="">Mes</label>
                            <input type="month" wire:model="selectedMonth" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="">Saldo Inicial Caixa</label>
                            <input type="number" wire:model="saldo_inicial_caixa"  class="form-control" placeholder="Saldo Inicial Caixa">
                        </div>
                        <div class="col-md-4">
                            <label for="">Saldo Inicial Santander</label>
                            <input type="number" wire:model="saldo_inicial_santander" class="form-control" placeholder="Saldo Inicial Santander">
                        </div>
                        <div class="col-md-4 mt-2">
                        </div>
                        <div class="col-md-4 mt-2">
                            <button wire:click="saveAndReload" class="btn btn-success">Guardar y Recargar</button>
                            <button wire:click="recalculateSaldos" class="btn btn-primary">Recalcular Saldos Iniciales</button>

                        </div>
                    </div>

                    <style>
                        td{
                            border: 1px solid #000 !important;
                        }
                    </style>

                    <table class="table text-center" border="1" style="width: 100%; border-collapse: collapse;">
                        <!-- Fila de encabezado que ocupa 12 columnas -->
                        <tr>
                            <th colspan="12">Enero 2025</th>
                        </tr>
                        <!-- Fila con Saldo Inicial en las columnas 5 y 7 -->
                        <tr>
                            <td colspan="4"></td>
                            <td class=" text-white text-center" style="background-color: #156082;">Saldo Inicial</td>
                            <td></td>
                            <td class=" text-white text-center" style="background-color: #C00000;">Saldo Inicial</td>
                            <td colspan="5"></td>
                        </tr>
                        <!-- Fila con Gastos y rowspan -->
                        <tr>
                            <td colspan="4" class="  text-center" >Gastos</td>
                            <td class=" text-white text-center" style="background-color: #156082;">{{$saldo_inicial_caixa}}</td>
                            <td></td>
                            <td class=" text-white text-center" style="background-color: #C00000;">{{$saldo_inicial_santander}}</td>

                            <td colspan="5" class=" text-center" >Ingresos</td>
                        </tr>
                        <!-- Fila vacía -->
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                        <!-- Fila con los encabezados de las columnas -->
                        <tr>
                            <td>FECHA</td>
                            <td>PROVEEDOR</td>
                            <td>IMPORTE</td>
                            <td>CARGO</td>
                            <td class="text-center text-white" style="background-color: #156082;">CAIXA</td>
                            <td>SALDO GLOBAL</td>
                            <td class="text-center text-white" style="background-color: #C00000;">SANTANDER</td>
                            <td>INGRESO</td>
                            <td>FECHA</td>
                            <td>Nº FACTURA</td>
                            <td>CLIENTE</td>
                            <td>IMPORTE</td>
                        </tr>
                        <!-- Fila vacía para completar el rowspan -->
                        <tr>
                            <td colspan="11"></td>
                        </tr>
                        @php
                            $totalCaixa = 0;
                            $totalSantander = 0;
                        @endphp
                        @foreach($dailyTransactions as $date => $banks)
                            @php
                                $ingresosBanco2 = collect($banks['banco2']['ingresos']);
                                $gastosBanco1 = collect($banks['banco1']['gastos']);
                                $maxCount =  max($ingresosBanco2->count(), $gastosBanco1->count());
                                $totalIngresosBanco2 = 0;
                                $totalGastosBanco1 = 0; 
                                $totalIngresosBanco1 = 0;
                                $totalGastosBanco2 = 0;
                                $isFirstRow = $loop->first ? true : false;
                            @endphp


                            <!-- Mostrar Ingresos del banco 2 y Gastos del banco 1 -->
                            @for($i =  $maxCount; $i >= 0; $i--)
                                @php
                                    $totalIngresosBanco2 += $ingresosBanco2[$i]->importe ?? 0;
                                    $totalGastosBanco1 += $gastosBanco1[$i]->importe ?? 0;
                                    $totalCaixa += $ingresosBanco2[$i]->santander ?? 0;
                                    $totalSantander += $gastosBanco1[$i]->santander ?? 0;
                                @endphp
                                <tr>

                                    <td @if(isset($ingresosBanco2[$i])) class="bg-danger text-white" @endif>{{$ingresosBanco2[$i]->factura->numero_factura ?? ''}}</td>
                                    <td @if(isset($ingresosBanco2[$i]) ) class="bg-danger text-white" @endif>{{ $ingresosBanco2[$i]->factura->cliente->nombre ?? '' }}</td>

                                    <td @if(isset($ingresosBanco2[$i])) class="bg-danger text-white" @endif>{{ $ingresosBanco2[$i]->importe ?? '' }} @if(isset($ingresosBanco2[$i])) € @endif</td>
                                    <td @if(isset($ingresosBanco2[$i]) && $i === 0) class="text-white" style="background-color: #C00000;" @endif> @if(isset($ingresosBanco2[$i]) && $i === 0) {{ $totalIngresosBanco2 ?? '' }} @if(isset($ingresosBanco2[$i])) € @endif @endif</td>
                                    <td class="text-center text-white" @if(isset($ingresosBanco2[$i]) && $i === 0) @endif>


                                        {{ $ingresosBanco2[$i]->santander ?? '' }}

                                    </td>
                                    <td></td> <!-- Columna central vacía -->
                                    <td></td>
                                    <td>{{ $gastosBanco1[$i]->proveedor->nombre ?? '' }}</td>
                                    <td>{{ $gastosBanco1[$i]->importe ?? '' }} @if(isset($gastosBanco1[$i])) € @endif</td>
                                    <td class="text-center text-white" @if(isset($gastosBanco1[$i]) && $i === 0) style="background-color: #156082;" @endif  >
                                        {{ $gastosBanco1[$i]->saldo ?? '' }}
                                    </td>
                                    <td>{{ $gastosBanco1[$i]->saldo_global ?? '' }}</td>
                                    <td></td>
                                </tr>
                            @endfor

                            <!-- Separar ingresos y gastos por 3 filas vacías si ambos existen -->
                            @if($ingresosBanco2->isNotEmpty() && $gastosBanco1->isNotEmpty())
                                <tr><td colspan="12"></td></tr>
                                <tr><td colspan="12"></td></tr>
                                <tr><td colspan="12"></td></tr>
                            @endif

                            @php
                                $gastosBanco2 = collect($banks['banco2']['gastos']);
                                $ingresosBanco1 = collect($banks['banco1']['ingresos']);
                                $maxCount = max($gastosBanco2->count(), $ingresosBanco1->count());
                            @endphp

                            @php
                                $totalGastosBanco2 = 0;
                                $totalIngresosBanco1 = 0;
                            @endphp


                            <!-- Mostrar Gastos del banco 2 y Ingresos del banco 1 -->
                            @for($i = $maxCount; $i >= 0; $i--)
                                @php
                                    $totalGastosBanco2 += $gastosBanco2[$i]->importe ?? 0;
                                    $totalIngresosBanco1 += $ingresosBanco1[$i]->importe ?? 0;
                                  


                                    
                                    $primerTotalCaixa =  $primerTotalCaixa ?? 0;
                                    $primerTotalSantander = $primerTotalSantander ?? 0;

                                    if($isFirstRow && $i === 0){
                                        $primerTotalCaixa = $saldo_inicial_caixa - $totalGastosBanco2 + $totalIngresosBanco1 ;

                                        $primerTotalSantander = $saldo_inicial_santander - $totalIngresosBanco2 + $totalGastosBanco1;


                                    }else{
                                        $primerTotalCaixa = $primerTotalCaixa - $totalGastosBanco2 + $totalIngresosBanco1;
                                        $primerTotalSantander = $primerTotalSantander - $totalIngresosBanco2 + $totalGastosBanco1;
                                    }

                                    $saldo_global = $primerTotalCaixa - $primerTotalSantander;



                                @endphp
                                <tr @if($i === 0) style="border-bottom: 2px solid #000; " @endif>



                                    <td @if( $i === 0) style="background-color: #c5c5c5; color: white" @endif>{{ $i === 0 ? $date : '' }}</td>
                                    <td>{{ $gastosBanco2[$i]->proveedor->nombre ?? '' }}</td>
                                    <td>{{ $gastosBanco2[$i]->importe ?? '' }} @if(isset($gastosBanco2[$i])) € @endif</td>

                                    <td class="text-center text-white" @if( $i === 0) style="background-color: #156082;" @endif>
                                        @if( $i === 0) {{ $totalGastosBanco2 ?? '0' }} @if( $i === 0) € @endif @endif
                                    </td>
                                    <td class="text-center text-white" @if($i === 0) style="background-color: #c5c5c5;" @endif>@if($i === 0) {{  $primerTotalCaixa ?? '' }} @endif</td>


                                    <td class="text-center text-white" @if($i === 0) style="background-color: #c5c5c5;" @endif>@if($i === 0) {{  $saldo_global ?? '' }} @endif</td>
                                    <td class="text-center text-white" @if($i === 0) style="background-color: #c5c5c5;" @endif>@if($i === 0) {{  $primerTotalSantander ?? '' }} @endif</td>
                                    <td @if( $i === 0) style="background-color: #156082; color: white" @endif> @if($i === 0) {{ $totalIngresosBanco1 ?? '0' }} @if($i === 0) € @endif @endif</td>
                                    <td @if( $i === 0) style="background-color: #c5c5c5; color: white" @endif> @if($i === 0) {{$date ?? ''}} @endif</td>

                                    <td> {{ $ingresosBanco1[$i]->factura->numero_factura ?? '' }}</td>
                                    <td  >
                                        {{ $ingresosBanco1[$i]->factura->cliente->nombre ?? '' }}
                                    </td>
                                    <td>{{ $ingresosBanco1[$i]->importe ?? '' }} @if(isset($ingresosBanco1[$i])) € @endif</td>
                                </tr>
                            @endfor

                            @php
                                $totalCaixa += $totalIngresosBanco1 + $totalGastosBanco1;
                                $totalSantander += $totalGastosBanco2 + $totalIngresosBanco2;
                            @endphp
                        @endforeach
                        <tr>
                            @php
                            $totalCaixa2 = 0;
                            $totalSantander2 = 0;
                                $totalCaixa2 = $saldo_inicial_caixa - $totalSantander + $totalCaixa;
                            @endphp
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{{ $totalSantander ?? 0 }} @if($totalSantander) € @endif</td>
                            <td> {{$totalCaixa2 ?? 0}} @if($totalCaixa2) € @endif</td>
                            <td></td>
                            <td></td>
                            <td>{{ $totalCaixa ?? 0 }} @if($totalCaixa) € @endif</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>

                    
                </div>

            </div>

                    
        </div>
        
    </div>

    @section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Escucha el evento 'updateUrl' emitido por Livewire
        Livewire.on('updateUrl', (params) => {
            const url = new URL(window.location.href);
            url.searchParams.set('page', params.page);
            window.history.pushState({}, '', url.toString());
        });
    
        // Lee la página actual de la URL al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            if (page) {
                Livewire.emit('setPage', page); // Notifica a Livewire la página actual
            }
        });
    </script>

    <script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
<script src="../assets/pages/datatables.init.js"></script>

    @endsection

    <style>
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none; /* Cambiado a none por defecto */
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            border: 8px solid #f3f3f3; /* Light grey */
            border-top: 8px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</div>
