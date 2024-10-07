<!DOCTYPE html>
<html>
    
<head>
    <title>Editar Acuerdo comercial</title>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> --}}
</head>    
    @section('head')

    @section('content-principal')
<div>
    @livewire('acuerdos-comerciales.edit-component', ['identificador' => $id])
</div>
</html>