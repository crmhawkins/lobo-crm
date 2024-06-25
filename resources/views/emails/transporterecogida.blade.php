<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        .container {
            width: 100%;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
        }
        .footer {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Detalles de su Recogida</h2>
        </div>
        <br>
        <p>Buenas tardes,</p>
        <p>Adjunto os envío el <strong>albarán {{ $num_albaran }}</strong></p>
        @if(!$datos['hasproductosFactura'])
            @php($pesoTotal = 0)
            @if(count($productos) > 0)
                @foreach ($productos as $producto )
                    @php($pesoTotal += $producto['peso_kg'])
                    <p>
                        @php($numeroCajas = $producto['num_cajas'] - ($producto['num_pallet'] * $producto['productos_pallet']) )
                        @php($unidades = $producto['cantidad'] - ($producto['num_cajas'] * $producto['productos_caja']) )
                        Producto: <strong>{{ $producto['nombre'] }} </strong> | 
                        Nº Pallets: <strong>{{ $producto['num_pallet'] }}  </strong> | 
                        Nº Cajas: <strong>{{ $numeroCajas }}</strong> |
                        Unidades: <strong>{{ $unidades }}</strong> |
                        Peso: <strong>{{ $producto['peso_kg'] }}kg</strong>
                    </p>
                
                @endforeach
                <p>peso Total : <strong>{{ $pesoTotal }}</strong> kg</p>
            @endif
        @else
            @php($pesoTotal = 0)
            @if(isset($productosFactura) && count($productosFactura) > 0)

            <table>
                <tr>
                    <th>Producto</th>
                    <th>Nº Pallets</th>
                    <th>Nº Cajas</th>
                    <th>Unidades</th>
                    <th>Peso</th>
                </tr>
                @foreach ($productosFactura as $product)
                        @php($pesoTotal += $product['peso_kg'])
                        @php($numeroCajas = $product['num_cajas'] - ($product['num_pallet'] * $product['productos_pallet']) )
                        @php($unidades = $product['cantidad'] - ($product['num_cajas'] * $product['productos_caja']) )
                    <tr>
                        <td>{{ $product['nombre'] }}</td>
                        <td>{{ $product['num_pallet'] }}</td>
                        <td>{{ $numeroCajas }}</td>
                        <td>{{ $unidades }}</td>
                        <td>{{ $product['peso_kg'] }}kg</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="4">Peso total</th>
                    <th>{{ $pesoTotal }}kg</th>
                </tr>
            </table>

            @endif

        @endif
        <br><br>
        <p style="text-transform: uppercase;">RETIRAR MERCANCIA: <strong>@if(isset($datos['destino'])) {{ $datos['destino']}}  @endif</strong></p>
        <p style="color:red; text-transform: uppercase; font-weight: bold;">@if(isset($datos['observacionesEmail'])) {{ $datos['observacionesEmail']}} @endif</p>
        <br>
        <p style="text-transform: uppercase;">ENTREGAR MERCANCIA: <strong>@if(isset($almacen)) {{ $almacen->direccion }}  @endif</strong></p>
        <p>HORARIO RECOGIDAS (ALMACÉN): <strong>@if(isset($almacen)) {{ $almacen->horario }}  @endif</strong></p>

    </div>
</body>
</html>
