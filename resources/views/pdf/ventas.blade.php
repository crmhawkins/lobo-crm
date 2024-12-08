<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        body {
            font-size: 12px;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Reporte de Ventas</h1>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Número</th>
                <th>Cliente</th>
                <th>Delegación</th>
                <th>Total</th>
                <th>Observaciones</th>
                @foreach($productos as $producto)
                    <th>{{ $producto->nombre }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($facturasSinPaginacion as $factura)
                <tr>
                    <td>{{ $factura->created_at->format('d-m-Y') }}</td>
                    <td>{{ $factura->numero_factura }}</td>
                    <td>{{ $factura->cliente->nombre }}</td>
                    <td>{{ $factura->cliente->delegacion->nombre ?? 'No definido' }}</td>
                    <td>{{ number_format($factura->total, 2) }}€</td>
                    <td>{{ $factura->descripcion }}</td>
                    @foreach($productos as $producto)
                        @php
                            $cantidadProducto = 0;
                            if ($factura->factura_id && $factura->productosFacturas) {
                                $productoFactura = $factura->productosFacturas->firstWhere('producto_id', $producto->id);
                                if ($productoFactura) {
                                    $cantidadProducto = -$productoFactura->cantidad;
                                }
                            } else if ($factura->pedido && $factura->pedido->productosPedido) {
                                $productoPedido = $factura->pedido->productosPedido->firstWhere('producto_pedido_id', $producto->id);
                                if ($productoPedido) {
                                    $cantidadProducto = $productoPedido->unidades;
                                }
                            }
                        @endphp
                        <td>{{ $cantidadProducto }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Totales de Ventas por Producto</h2>
    <table>
        <thead>
            <tr>
                @foreach($productos as $producto)
                    <th>{{ $producto->nombre }}</th>
                @endforeach
                <th>Total General</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($productos as $producto)
                    <td>{{ $producto->total_unidades_vendidas }}</td>
                @endforeach
                <td><strong>{{ number_format($totalEurosFacturas, 2) }}€</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html> 