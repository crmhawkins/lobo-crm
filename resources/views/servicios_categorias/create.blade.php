
@extends('layouts.app')

@section('title', 'Crear Categoria de Servicio')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios-categorias.create-component')
</div>
@endsection

