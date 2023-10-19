@extends('layouts.app')

@section('title', 'Ver Tipos de Gasto')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('tipo-gasto.index-component')
</div>
@endsection