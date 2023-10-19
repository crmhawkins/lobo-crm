@extends('layouts.app')

@section('title', 'Crear Categoria de Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('categoria-evento.create-component')
</div>
@endsection