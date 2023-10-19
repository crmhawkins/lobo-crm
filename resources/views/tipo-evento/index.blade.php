@extends('layouts.app')

@section('title', 'Ver Tipos de Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('tipo-evento.index-component')
</div>
@endsection