
@extends('layouts.app')

@section('title', 'Crear pedido')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

    @section('content-principal')
<div>
    @livewire('comercial.createpedido')
</div>
@endsection


