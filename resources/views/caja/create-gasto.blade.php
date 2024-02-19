@extends('layouts.app')

@section('title', 'Movimiento de Caja')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('caja.create-gasto-component')
</div>
@endsection

