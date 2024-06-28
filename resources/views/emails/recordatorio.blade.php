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
        .headerWarning {
            background-color: #ff9800;
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
        @if($tipo == 'impago')
            <div class="headerWarning">
                <h2>Factura vencida</h2>
            </div>
            <br>
            <p>Estimado/a {{ $cliente->nombre }}, <br>
                <br>
                Espero que este mensaje le encuentre bien. 
                Nos dirigimos a usted para informarle que la factura con número {{ $factura->numero_factura }} 
                por un monto de {{ $factura->total }}€ ha vencido.
                <br><br>
                Agradecemos mucho su confianza en nuestros servicios y entendemos que a veces pueden surgir situaciones que afecten el 
                cumplimiento de los plazos establecidos. Por ello, le solicitamos amablemente que proceda con el pago del 
                importe pendiente a la brevedad posible, para evitar cualquier inconveniente adicional.
                <br><br>
                Agradecemos de antemano su pronta atención a este asunto y quedamos a su disposición para cualquier consulta que pueda surgir al respecto.
                <br><br>
                Quedamos a la espera de su respuesta y le enviamos un cordial saludo.
            
            </p>
        @else
        <div class="headerWarning">
            <h2>Factura Pendiente</h2>
        </div>
        <br>
        <p>Estimado/a {{ $cliente->nombre }}, <br>
            <br>
            Esperamos que se encuentre bien. Nos dirigimos a usted para recordarle que la fecha de vencimiento de su factura número {{ $factura->numero_factura }} está próxima.
            <br><br>
            La fecha de vencimiento es el {{ $factura->fecha_vencimiento }}. Le agradecemos realizar el pago correspondiente antes de esta fecha para evitar cualquier inconveniente.
            <br><br>
            Si ya ha efectuado el pago, por favor, ignore este mensaje. En caso contrario, le solicitamos que realice el pago a la brevedad. Puede encontrar los detalles de la factura y las instrucciones de pago adjuntas a este correo.
            <br><br>
            Para cualquier duda o consulta, no dude en contactarnos. Estamos a su disposición para asistirle en lo que necesite.
            <br><br>
            Agradecemos su atención y colaboración.
        </p>
        @endif

        <div class="header">
            <h2>Detalles de su Factura</h2>
        </div>

        <br>

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
                    @if(isset($datos['rectificada']))
                        <td>{{ number_format($datos['base_imponible'], 2) }}€</td>
                    @else
                        <td>{{ number_format($datos['factura']->precio, 2) }}€</td>
                    @endif
                </tr>
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>IVA 21%</td>
                    @if(isset($datos['rectificada']))
                        <td>{{number_format($datos['iva_productos'], 2)}}€</td>
                    @else
                        <td>{{number_format($datos['factura']->precio * 0.21, 2)}}€</td>
                    @endif
                </tr>
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>TOTAL</td>
                    @if(isset($datos['rectificada']))
                    <td>{{number_format($datos['total'], 2)}}€</td>
                    @else
                        <td>{{number_format($datos['factura']->precio * 1.21, 2)}}€</td>
                    @endif
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
        <div class="footer">
            <p>Si tiene alguna pregunta acerca de su pedido, no dude en contactarnos.</p>
            <p>Saludos cordiales,</p>
        </div>
    </div>
</body>
</html>
