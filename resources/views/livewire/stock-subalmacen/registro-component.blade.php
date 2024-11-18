<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">REGISTRO DE STOCK</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Registro de Stock</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Filtro de búsqueda y filtro de mes -->
    <div class="row mb-4">
        <div class="col-md-4">
            <input type="text" wire:model.debounce.300ms="search" class="form-control" placeholder="Buscar por producto o subalmacén...">
        </div>
        <div class="col-md-4">
            <!-- Selector de mes -->
            <input type="month" wire:model="selectedMonth" class="form-control" placeholder="Seleccionar mes">
        </div>
        <div class="col-md-4 text-right">
            <select wire:model="perPage" class="form-control w-auto">
                <option value="10">10 Registros</option>
                <option value="25">25 Registros</option>
                <option value="50">50 Registros</option>
                <option value="100">100 Registros</option>
            </select>
        </div>
    </div>

    <!-- Tabla de registros de stock -->
    <div class="card m-b-30">
        <div class="card-body">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Subalmacén</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registros as $registro)
                        <tr>
                            <td>{{ $registro->subalmacen->almacen }}</td>
                            <td>{{ $registro->producto->nombre ?? '' }}</td>
                            <td>{{ $registro->tipo_entrada ? 'Entrada' : 'Salida' }}</td>
                            <td>{{ $registro->cantidad }}</td>
                            <td>{{ \Carbon\Carbon::parse($registro->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $registro->tipo_entrada ? $registro->tipo_entrada : $registro->tipo_salida }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron registros de stock</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-end">
                {{ $registros->links() }}
            </div>
        </div>
    </div>
</div>
