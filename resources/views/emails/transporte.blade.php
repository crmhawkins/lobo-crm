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

        <p>Buenas tardes,</p>
        <p>Adjunto os envío el <strong>albarán {{ $pedido->id }}</strong></p>
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
        
        <p>Los datos y direccion de <strong>Entrega</strong> es:</p>
        <p><strong>{{ $cliente->nombre }} </strong></p>
        <p><strong>{{ $cliente->direccionenvio }} </strong></p>
        <p><strong>{{ $cliente->codPostalenvio }} - {{ $cliente->localidadenvio }}({{ $cliente->provinciaenvio }})</strong></p>
        <br>
        <p style="color:red; text-transform: uppercase; font-weight: bold;">@if(isset($observaciones)) {{ $observaciones }} @endif</p>
        <br>
        <p style="text-transform: uppercase;">RETIRAR MERCANCIA: <strong>@if(isset($almacen)) {{ $almacen->direccion }}  @endif</strong></p>
        <p>HORARIO RECOGIDAS (ALMACÉN): <strong>@if(isset($almacen)) {{ $almacen->horario }}  @endif</strong></p>

    </div>
</body>
</html>
