@extends('layouts.app')

@section('title', 'Añadir mercadería')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('mercaderia.create-component')
</div>
@endsection

