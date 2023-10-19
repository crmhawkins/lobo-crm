@extends('layouts.app')

@section('title', 'Crear Tipo de Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('tipo-evento.create-component')
</div>
@endsection