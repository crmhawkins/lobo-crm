@extends('layouts.app')

@section('title', 'Ver Servicios')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios.index-component')
</div>
@endsection