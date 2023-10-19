@extends('layouts.app')

@section('title', 'Crear Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('eventos.create-component')
</div>
@endsection

