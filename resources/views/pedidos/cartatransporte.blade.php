<!DOCTYPE html>
<html>
    
<head>
    <title>Carta de transporte</title>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> --}}
</head>    
    @section('head')

    @section('content-principal')
    <div>
        @livewire('pedidos.cartatransporte', ['identificador' => $id])
    </div>
</html>


