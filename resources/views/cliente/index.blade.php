
@extends('layouts.app')

@section('title', 'Ver Clientes')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('clientes.index-component')
</div>
@endsection