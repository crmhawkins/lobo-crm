
@extends('layouts.app')

@section('title', 'Crear Servicio')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios.create-component')
</div>
@endsection

