



@extends('layouts.app')

@section('title', 'Ver Presupuestos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('presupuestos.index-component')
</div>
@endsection