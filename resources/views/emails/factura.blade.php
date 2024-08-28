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
            <h2>Detalles de su Factura</h2>
        </div>

        <p>Hola {{ $datos['cliente']->nombre }},</p>
        @if(isset($pedido))
            <p>Gracias por su pedido. Aquí están los detalles de su pedido realizado el {{ $pedido->fecha }}:</p>
        @endif
        @if($datos['servicios'] != null)
            <p>Gracias por su pedido. Aquí están los detalles de su factura:</p>
        @endif
        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio por unidad</th>
                <th>Subtotal</th>
            </tr>
            
            @if($datos['pedido'] != null )

                @foreach ($datos['productos'] as $producto)
                
                    <tr>
                        <td>{{ $producto['nombre'] }}</td>
                        <td>{{ $producto['cantidad'] }}</td>
                        <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
                        <td>{{ number_format($producto['precio_total'], 2) }}€</td>
                    </tr>
                @endforeach
            @elseif( $datos['producto'] != null)
                <td>{{ $datos['producto']->nombre }}</td>
                <td>{{ $datos['producto']->cantidad }}</td>
                <td>{{ $datos['producto']->precio}}€</td>
                <td>{{ number_format($datos['factura']->precio, 2) }}€</td>
            @endif

            @if($datos['servicios'] != null && count($datos['servicios']) > 0)

                
                @foreach ($datos['servicios'] as $servicio )
                    <tr>
                        <td>{{ $servicio->descripcion }}</td>
                        <td>{{ $servicio->cantidad }}</td>

                        <td>{{ $servicio->precio }}</td>

                        <td>{{ number_format($servicio->total, 2) }}€</td>
                    </tr>

                @endforeach
                   
            @endif
        </table>
        @if($datos['conIva'])
            <table style="margin-top: 5% !important">
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>BASE IMPONIBLE</td>
                    <td>{{ number_format($datos['factura']->precio, 2) }}€</td>
                </tr>
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>IVA 21%</td>
                    <td>{{number_format($datos['factura']->precio * 0.21, 2)}}€</td>
                </tr>
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>TOTAL</td>
                    <td>{{number_format($datos['factura']->precio * 1.21, 2)}}€</td>
                </tr>
            </table>
        @else
            <table style="margin-top: 5% !important">
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>Total</td>
                    <td>{{ number_format($datos['factura']->precio, 2) }}€</td>
                </tr>
            </table>
        @endif
        @if(isset($datos['pedido']))
            @if ($datos['pedido']->descuento)
                <p>Se ha aplicado un descuento en su pedido.</p>
            @endif
        @endif
        @if(isset($datos['pedido']))
            <p>Observaciones:</p>
            <p>{{ $datos['pedido']->observaciones }}</p>
        @endif
        @if(isset($datos['anotacionesEmail']) && $datos['anotacionesEmail'] != null)
        <br><br>
            <p><strong>Anotaciones: </strong><br>
            {{ $datos['anotacionesEmail'] }}</p>
        @endif
        @if(isset($pedido->fecha_salida) && isset($pedido->empresa_transporte))
            <p>SU PEDIDO HA SALIDO DE NUESTRAS INSTALACIONES A FECHA {{ $pedido->fecha_salida }} CON LA EMPRESA {{ $pedido->empresa_transporte }}</p>
        @endif
        <div class="footer">
            <p>Si tiene alguna pregunta acerca de su pedido, no dude en contactarnos.</p>
            <p>Saludos cordiales,</p>
            <br>
            <p>{{ $configuracion->texto_email }}</p>
        </div>

    </div>
</body>
</html>
