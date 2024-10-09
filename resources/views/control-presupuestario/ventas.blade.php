@extends('layouts.app')

@section('title', 'Control Presupuestario PTO. VENTAS')

@section('head')
    @vite(['resources/sass/productos.scss', 'resources/sass/alumnos.scss'])
    <style>
        ul.pagination {
            justify-content: center;
        }

        /* Evita que las celdas se dividan en varias líneas */
        table th, table td {
            white-space: nowrap;
        }
    </style>
@endsection

@section('content-principal')
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CONTROL PTO. VENTAS</h4>
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
        <div class="col-md-12">
            <form method="GET" action="{{ route('control-presupuestario.ventas') }}">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Buscar por número de factura o cliente" value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <input type="date" name="fechaMin" class="form-control" value="{{ request('fechaMin') }}" placeholder="Fecha mínima">
                    </div>

                    <div class="col-md-3">
                        <input type="date" name="fechaMax" class="form-control" value="{{ request('fechaMax') }}" placeholder="Fecha máxima">
                    </div>

                    <div class="col-md-3 mt-2">
                        <select name="perPage" class="form-control" style="width: auto; display: inline-block;">
                            <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10 por página</option>
                            <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 por página</option>
                            <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 por página</option>
                        </select>
                    </div>

                    <div class="col-md-3 mt-2">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Tabla principal con scroll horizontal -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Número</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Delegación</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Total</th>
                            <th scope="col">Observaciones</th>
                            <!-- Generar columnas para cada producto -->
                            @foreach($productos as $producto)
                                <th scope="col">{{ $producto->nombre }}</th>
                            @endforeach
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($facturas as $factura)
                            <tr>
                                <td>{{ $factura->created_at->format('d-m-Y') }}</td>
                                <td>{{ $factura->numero_factura }}</td>
                                <td>{{ $factura->cliente->nombre }}</td>
                                <td>{{ $factura->cliente->delegacion->nombre ?? 'No definido' }}</td>
                                <td>{{ $factura->created_at->format('d-m-Y') }}</td>
                                @if($factura->factura_id)
                                  @php
                                    if($factura->hasIva){
                                        $totalFactura = $factura->total - $factura->facturaNormal->total ;
                                    }else{
                                        $totalFactura = $factura->precio - $factura->facturaNormal->precio;
                                    }
                                  @endphp
                                    <td>{{ number_format($totalFactura, 2) }}€ </td>
                                @else
                                    @if($factura->hasIva)
                                        <td>{{ number_format($factura->total, 2) }}€</td>
                                    @else
                                        <td>{{ number_format($factura->precio, 2) }}€</td>
                                    @endif

                                @endif
                                <td>{{ $factura->descripcion  }}</td>

                                <!-- Mostrar las cantidades para cada producto -->
                                @foreach($productos as $producto)
                                    @php
                                        $cantidadProducto = 0;
                                         // Si es una factura rectificativa, restamos las unidades descontadas
                                         if ($factura->factura_id && $factura->productosFacturas) {
                                            $productoFactura = $factura->productosFacturas->firstWhere('producto_id', $producto->id);
                                            if ($productoFactura) {
                                                $cantidadProducto = -$productoFactura->cantidad;
                                            }
                                        }else if ($factura->pedido && $factura->pedido->productosPedido) {
                                            $productoPedido = $factura->pedido->productosPedido->firstWhere('producto_pedido_id', $producto->id);
                                            if ($productoPedido) {
                                                $cantidadProducto = $productoPedido->unidades;
                                            }
                                        }
                                        
                                       
                                    @endphp
                                    <td>{{ $cantidadProducto }}</td>
                                @endforeach

                                <td>
                                    <a href="{{ route('facturas.edit', ['id' => $factura->id]) }}" class="btn btn-primary btn-sm fw-bold"> Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Añadir la paginación -->
            <div class="d-flex justify-content-center">
                {{ $facturas->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

     <!-- Segunda tabla con el total de botellas vendidas y el total en euros por cada producto -->
    <div class="row mt-5 mb-5">
        <div class="col-md-12">
            <h4 class="mt-0 header-title">Totales de Ventas por Producto</h4>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <!-- Generar columnas para cada producto -->
                            @foreach($productos as $producto)
                                <th scope="col">{{ $producto->nombre }}</th>
                            @endforeach
                            <th scope="col">Total General</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <!-- Mostrar las unidades vendidas para cada producto -->
                            @foreach($productos as $producto)
                                <td>{{ $producto->total_unidades_vendidas }}</td>
                            @endforeach
                            <td><strong>{{ number_format($totalEurosFacturas, 2) }}€</strong></td>
                        </tr>
                        {{-- <tr>
                            <!-- Mostrar el total en euros para cada producto -->
                            @foreach($productos as $producto)
                                <td>{{ number_format($producto->total_euros_vendidos, 2)  }}€</td>
                            @endforeach
                            <!-- Mostrar el total de todas las facturas al final -->
                            
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
