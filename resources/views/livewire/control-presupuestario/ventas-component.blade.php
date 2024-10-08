<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">VENTAS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Control Presupuestario</a></li>
                    <li class="breadcrumb-item active">Ventas</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Buscar por número de factura o cliente" wire:model.debounce.500ms="search">
        </div>

        <div class="col-md-3">
            <input type="date" class="form-control" wire:model="fechaMin" placeholder="Fecha mínima">
        </div>
        
        <div class="col-md-3">
            <input type="date" class="form-control" wire:model="fechaMax" placeholder="Fecha máxima">
        </div>
        
        <div class="col-md-12 text-right mt-2">
            <select wire:model="perPage" class="form-control" style="width: auto; display: inline-block;">
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Ventas</h4>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">Número</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facturas as $factura)
                                <tr>
                                    <td>{{ $factura->numero_factura }}</td>
                                    <td>{{ $factura->cliente->nombre }}</td>
                                    <td>{{ $factura->created_at->format('d-m-Y') }}</td>
                                    <td>{{ number_format($factura->total, 2) }}€</td>
                                    <td>
                                        <a href="{{ route('facturas.edit', ['id' => $factura->id]) }}" class="btn btn-primary btn-sm fw-bold"> Ver</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Paginación con diseño de Bootstrap -->
                    {{ $facturas->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
