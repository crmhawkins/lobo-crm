<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">GESTIÓN DE STOCK DE SUBALMACENES</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active">Stock en Subalmacenes</li>
                </ol>
            </div>
        </div>
    </div>
<!-- Botón para ver el registro de stock -->
<div class="row mb-4 d-flex gap-2">
    <div class="col-md-12 text-right">
        <a href="{{ route('productosmarketing.index') }}" class="btn btn-primary">Productos</a>
    </div>
    <div class="col-md-12 text-right">
        <a href="{{ route('stock-subalmacen.registro') }}" class="btn btn-primary">Ver Registro de Stock</a>
    </div>
</div>
    <!-- Selector de Subalmacenes -->
    <div class="row">
        <div class="col-md-6">
            <label for="subalmacen" class="col-form-label">Seleccionar Subalmacén</label>
            <select class="form-control" id="subalmacen" wire:model="selectedSubalmacen">
                @foreach($almacenes as $almacen)
                    <option value="{{ $almacen->id }}">{{ $almacen->almacen }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Productos y Stock -->
    <div class="row mt-4">
        @if (count($productos) > 0)
            <div class="col-12">
                <h4>Productos en el Subalmacén: {{ $almacenSeleccionado->almacen }}</h4>
                <div class="card m-b-30">
                    <div class="card-body">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock Actual</th>
                                    <th>Cantidad a Modificar</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productos as $producto)
                                    <tr>
                                        <td>{{ $producto->nombre }}</td>
                                        <td>
                                            <!-- Usar el método stockActual del modelo StockSubalmacen -->
                                            {{ \App\Models\StockSubalmacen::stockActual($selectedSubalmacen, $producto->id) }}
                                        </td>
                                        <td>
                                            <input type="number" min="1" step="1" wire:model.defer="cantidad.{{ $selectedSubalmacen }}.{{ $producto->id }}" class="form-control" placeholder="Cantidad">
                                        </td>
                                        <td>
                                            <button class="btn btn-success mr-2" wire:click="añadirStock({{ $selectedSubalmacen }}, {{ $producto->id }})">Añadir</button>
                                            <button class="btn btn-danger" wire:click="reducirStock({{ $selectedSubalmacen }}, {{ $producto->id }})">Reducir</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <h6 class="text-center">No hay productos disponibles</h6>
            </div>
        @endif
    </div>
</div>
