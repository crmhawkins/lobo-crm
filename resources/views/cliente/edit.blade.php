@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('clientes.edit-component', ['identificador'=>$id])
</div>
@endsection


