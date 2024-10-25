@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('comercial.editcliente', ['identificador'=>$id])
</div>
@endsection


