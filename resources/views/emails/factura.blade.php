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

        <p>Hola {{ $cliente->nombre }},</p>
        @if(isset($pedido))
            <p>Gracias por su pedido. Aquí están los detalles de su pedido realizado el {{ $pedido->fecha }}:</p>
        @endif
        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio por unidad</th>
                <th>Subtotal</th>
            </tr>
            @if(isset($pedido))
            @foreach ($productos as $producto)
                <tr>
                    <td>{{ $producto['nombre'] }}</td>
                    <td>{{ $producto['cantidad'] }}</td>
                    <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
                    <td>{{ number_format($producto['precio_total'], 2) }}€</td>
                </tr>
            @endforeach
            @else
            <td>{{ $producto->nombre }}</td>
            <td>{{ $factura->cantidad }}</td>
            <td>{{ $producto->precio}}€</td>
            <td>{{ number_format($factura->precio, 2) }}€</td>
            @endif
        </table>
        @if($datos['conIva'])
            <table style="margin-top: 5% !important">
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>BASE IMPONIBLE</td>
                    <td>{{ number_format($factura->precio, 2) }}€</td>
                </tr>
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>IVA 21%</td>
                    <td>{{number_format($factura->precio * 0.21, 2)}}€</td>
                </tr>
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>TOTAL</td>
                    <td>{{number_format($factura->precio * 1.21, 2)}}€</td>
                </tr>
            </table>
        @else
            <table style="margin-top: 5% !important">
                <tr style="background-color:#ececec;">
                    <td colspan="3"></td>
                    <td>Total</td>
                    <td>{{ number_format($factura->precio, 2) }}€</td>
                </tr>
            </table>
        @endif
        @if(isset($pedido))
            @if ($pedido->descuento)
                <p>Se ha aplicado un descuento en su pedido.</p>
            @endif
        @endif
        @if(isset($pedido))
            <p>Observaciones:</p>
            <p>{{ $pedido->observaciones }}</p>
        @endif
        <div class="footer">
            <p>Si tiene alguna pregunta acerca de su pedido, no dude en contactarnos.</p>
            <p>Saludos cordiales,</p>
        </div>
    </div>
</body>
</html>
