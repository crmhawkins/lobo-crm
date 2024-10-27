@extends('layouts.app')

@section('title', 'Editar pedido')

@section('head')
@vite(['resources/sass/productos.scss'])
@vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('comercial.editpedido', ['identificador' => $id])
</div>
@endsection
