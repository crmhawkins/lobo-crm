@extends('layouts.app')

@section('title', 'Ver Categoria de Servicios')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios-categorias.index-component')
</div>
@endsection