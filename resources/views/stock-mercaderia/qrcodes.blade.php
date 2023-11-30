<!DOCTYPE html>
<html>

<head>
    <title>Códigos QR para asignación de stock (Mercadería)</title>
    <style>
        .qr-code {
            display: inline-block;
            margin: 10px;
        }

        @page {
            margin-left: 4% !important;
            margin-right: 3% !important;
            margin-bottom: 0 !important;
            padding: 0 !important;
        }
    </style>
</head>

<body>
    <div>
        <h2 style="text-align: center">Códigos QR para asignación de stock (Mercadería)</h2>
        <br>
        @foreach ($qrcodes as $qr)
            <img class="qr-code" src="data:image/png;base64, {!! base64_encode($qr) !!}">
        @endforeach
    </div>
</body>

</html>
