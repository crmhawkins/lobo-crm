
@extends('layouts.app')

@section('title', 'Ver Articulos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('articulos.index-component')
</div>
@endsection