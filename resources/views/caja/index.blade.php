@extends('layouts.app')

@section('title', 'Ver Movimiento de Caja')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('caja.index-component')
</div>
@endsection
