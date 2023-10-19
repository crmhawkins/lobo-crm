@extends('layouts.app')

@section('title', 'Ver Categorías de Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('categoria-evento.index-component')
</div>
@endsection