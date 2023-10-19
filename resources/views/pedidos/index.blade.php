@extends('layouts.app')

@section('title', 'Ver pedidos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('pedidos.index-component')
</div>
@endsection
