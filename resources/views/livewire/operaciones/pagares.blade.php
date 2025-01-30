<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Pagares</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Operaciones</a></li>
                    <li class="breadcrumb-item active">Pagares</li>
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
                            <label for="mes">Mes</label>
                            <select wire:model="mes" id="mes" class="form-control">
                                @php
                                    \Carbon\Carbon::setLocale('es');
                                @endphp
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="anio">Año</label>
                            <select wire:model="anio" id="anio" class="form-control">
                                @foreach(range(2020, Carbon\Carbon::now()->year) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <style>
                        td {
                            border: 1px solid #000 !important;
                        }
                    </style>

                    <table class="table text-center" border="1" style="width: 100%; border-collapse: collapse;" >
                        <thead>
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Importe</th>
                                <th>F. Factura</th>
                                <th>Nº Pagos</th>
                                <th>Importes Efectos</th>
                                <th>F. Efecto</th>
                                <th>Nº Efecto</th>
                                <th>Banco</th>
                                <th>Estado</th>
                                <th>Compensación</th>
                                <th>Observación Compensación</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($cajas as $index => $caja)

                                
                                    {{-- {{dd($caja)}} --}}
                                    <tr>
                                        <td rowspan="{{ (count($caja['pagares']) ?? 1) + 1 }}" ><a class="badge badge-primary" href="{{ route('caja.edit', $caja['id']) }}">{{ $caja['nFactura'] }}</a></td>
                                        <td rowspan="{{ (count($caja['pagares']) ?? 1) + 1 }}"><a class="badge badge-primary" href="{{ route('proveedores.edit', $caja['proveedor']['id']) }}">{{ $caja['proveedor']['nombre'] ?? 'N/A' }}</a></td>
                                        <td rowspan="{{ (count($caja['pagares']) ?? 1) + 1 }}">{{ $caja['total'] }} @if($caja['total']) € @endif</td>
                                        <td rowspan="{{ (count($caja['pagares']) ?? 1) + 1 }}">{{ $caja['fecha'] }}</td>
                                        <td rowspan="{{ (count($caja['pagares']) ?? 1) + 1 }}">
                                            <select wire:model="nPagos.{{ $index }}" class="form-control">
                                                @for($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}" {{ $i == $nPagos[$index] ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </td>
                                        <td colspan="5"></td>
                                        <td rowspan="{{ (count($caja['pagares']) ?? 1) + 1 }}">
                                            @if(isset($caja['facturas_compensadas']) && count($caja['facturas_compensadas']) > 0)
                                               @php
                                                    $compensacion = 0;
                                                    foreach ($caja['facturas_compensadas'] as $item) {
                                                        $compensacion += $item['pagado'];
                                                    }
                                               @endphp
                                               {{ $compensacion }} @if($compensacion) € @endif
                                            @else
                                                <small>Sin compensar</small>
                                            @endif
                                        </td>
                                        <td rowspan="{{ (count($caja['pagares']) ?? 1) + 1 }}">
                                            {{-- {{dd($caja)}} --}}

                                            @if(isset($caja['facturas_compensadas']) && count($caja['facturas_compensadas']) > 0)
                                                @foreach ($caja['facturas_compensadas'] as $item)
                                                    {{-- {{dd($item['factura'])}} --}}
                                                    @if($item['factura'])
                                                        <small>{{ $item['factura']['numero_factura'] }}</small> 
                                                    @endif
                                                @endforeach
                                            @else
                                                <small>Sin compensar</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @foreach($caja['pagares'] as $pagareIndex => $pagare)
                                        <tr>
                                            {{-- {{dd($cajas[$index]['pagares'][$pagareIndex]['importe_efecto'])}}  --}}
                                            <td>
                                                <input type="text" wire:model="cajas.{{ $index }}.pagares.{{ $pagareIndex }}.importe_efecto" class="form-control" value="{{ $pagare['importe_efecto'] }}" />
                                            </td>
                                            <td>    
                                                <input type="date" wire:model="cajas.{{ $index }}.pagares.{{ $pagareIndex }}.fecha_efecto" class="form-control" />
                                            </td>
                                            <td>
                                                <input type="text" wire:model="cajas.{{ $index }}.pagares.{{ $pagareIndex }}.nEfecto" class="form-control" />

                                            </td>
                                            <td>
                                                <select wire:model="cajas.{{ $index }}.pagares.{{ $pagareIndex }}.banco_id" class="form-control">
                                                    @foreach($bancos as $banco)
                                                        <option value="{{ $banco->id }}" {{ $banco->id == $pagare['banco_id'] ? 'selected' : '' }}>{{ $banco->nombre }}</option>
                                                    @endforeach
                                                </select>

                                            </td>
                                            <td>
                                                <select wire:model="cajas.{{ $index }}.pagares.{{ $pagareIndex }}.estado" class="form-control">
                                                    <option value="pendiente" {{ $pagare['estado'] == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                    <option value="pagado" {{ $pagare['estado'] == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                                </select>                                            </td>
                                            
                                        </tr>
                                    @endforeach

                                    
                            @endforeach
                        </tbody>
                    </table>

                    {{-- {{ $pagares->links() }} --}}
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        Livewire.on('updateUrl', (params) => {
            const url = new URL(window.location.href);
            url.searchParams.set('page', params.page);
            window.history.pushState({}, '', url.toString());
        });

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            if (page) {
                Livewire.emit('setPage', page);
            }
        });
    </script>
    @endsection

    <style>
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
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
