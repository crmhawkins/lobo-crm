<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .header-table, .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 5px;
        }
        .logo {
            width: 100px;
        }
        .company-details {
            text-align: left;
            vertical-align: top;
        }
        .order-details {
            text-align: right;
            vertical-align: top;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .details-table th {
            background-color: #f2f2f2;
            color: black;
        }
        .footer-table {
            margin-top: 20px;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td><img src="{{ public_path('images/logo_head.png') }}" alt="Logo" class="logo"></td>
            <td class="company-details">
                <strong>LOBO DEL SUR S.L.</strong><br>
                B16914285<br>
                PLAZA DEL VILLAR 8, PLANTA 1-A<br>
                LOS BARRIOS (11370), Cádiz, España<br>
                administracion@serlobo.com | 654183607
            </td>
            <td class="order-details">
                <strong>Pedido #{{ $pedido->id }}</strong><br>
                Fecha: {{ $pedido->fecha }}<br>
                Cliente: {{ $cliente->nombre }}
            </td>
        </tr>
    </table>

    <h2>Detalles del Pedido</h2>
    <table class="details-table">
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total</th>
        </tr>
        @foreach ($productos as $producto)
        <tr>
            <td>{{ $producto['nombre'] }}</td>
            <td>{{ $producto['cantidad'] }}</td>
            <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
            <td>{{ number_format($producto['precio_total'], 2) }}€</td>
        </tr>
        @endforeach
    </table>

    <table class="footer-table">
        <tr>
            <td>Total Pedido: {{ number_format($pedido->precio, 2) }}€</td>
        </tr>
        <tr>
            <td>Descuento Aplicado: {{ $pedido->descuento ? 'Sí' : 'No' }}</td>
        </tr>
    </table>

    <p>Observaciones: {{ $pedido->observaciones }}</p>

    <p class="footer-table">
        Gracias por su pedido.<br>
        Para cualquier consulta, no dude en contactarnos.
    </p>
</body>
</html>
