



@extends('layouts.app')

@section('title', 'Ver Presupuestos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
    <meta charset="UTF-8">
@endsection

@section('content-principal')
    @livewire('resumen-semanas.show-component')
</div>

@endsection

