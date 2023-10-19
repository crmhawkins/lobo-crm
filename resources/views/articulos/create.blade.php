@extends('layouts.app')

@section('title', 'Crear Articulo')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('articulos.create-component')
</div>
@endsection
