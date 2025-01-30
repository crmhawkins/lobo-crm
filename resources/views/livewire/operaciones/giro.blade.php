
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Giro Bancario</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Operaciones</a></li>
                    <li class="breadcrumb-item active">Giro Bancario</li>
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
                            <select wire:model="anio" id="anio" class="form-control" >
                                @foreach(range(Carbon\Carbon::now()->year, 2020) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Factura</th>
                                <th>Cliente</th>
                                <th>Nº Cuenta</th>
                                <th>Importe</th>
                                <th>F. Vencimiento</th>
                                <th>Banco</th>
                                <th>F. Programación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($facturas as $item)
                                <tr>
                                    <td><a class="btn btn-primary" href="{{ route('facturas.edit', $item->id) }}">{{ $item->numero_factura }}</a></td>
                                    <td><a class="btn btn-primary" href="{{ route('clientes.edit', $item->cliente_id) }}">{{ $item->cliente->nombre }}</a></td>
                                    <td>{{ $item->cliente->cuenta }}</td>
                                    <td>{{ $item->total }}</td>
                                    <td>{{ $item->fecha_vencimiento }}</td>
                                    @if(isset($editing[$item->id]) || $item->giro_bancario)
                                        <td>
                                            <select wire:model="giroData.{{ $item->id }}.banco_id" class="form-control">
                                                @foreach($bancos as $banco)
                                                    <option value="{{ $banco->id }}">{{ $banco->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="date" wire:model="giroData.{{ $item->id }}.fecha_programacion" class="form-control">
                                        </td>
                                        <td>
                                            <select wire:model="giroData.{{ $item->id }}.estado" class="form-control">
                                                <option value="Pendiente">Pendiente</option>
                                                <option value="Programado">Pagado</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button wire:click="saveGiro({{ $item->id }})" class="btn btn-success">Guardar</button>
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <button wire:click="editGiro({{ $item->id }})" class="btn btn-primary">Editar</button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <style>
                        td {
                            border: 1px solid #000 !important;
                        }
                    </style>

                    {{-- {{ $pagares->links() }} --}}
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   
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
