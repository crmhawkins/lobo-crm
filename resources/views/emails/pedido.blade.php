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
            <h2>Detalles de su Pedido</h2>
        </div>

        <p>Hola {{ $cliente->nombre }},</p>
        <p>Gracias por su pedido. Aquí están los detalles de su pedido realizado el {{ $pedido->fecha }}:</p>

        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio por unidad</th>
                <th>Subtotal</th>
            </tr>
            @foreach ($productos as $producto)
                <tr>
                    <td>{{ $producto['nombre'] }}</td>
                    <td>{{ $producto['cantidad'] }}</td>
                    <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
                    <td>{{ number_format($producto['precio_total'], 2) }}€</td>
                </tr>
            @endforeach
            <tr>
                <th colspan="3">Total</th>
                <th>{{ number_format($pedido->precio, 2) }} €</th>
            </tr>
        </table>

        @if ($pedido->descuento)
            <p>Se ha aplicado un descuento en su pedido.</p>
        @endif

        <p>Observaciones:</p>
        <p>{{ $pedido->observaciones }}</p>

        <div class="footer">
            <p>Si tiene alguna pregunta acerca de su pedido, no dude en contactarnos.</p>
            <p>Saludos cordiales,</p>
        </div>
    </div>
</body>
</html>
