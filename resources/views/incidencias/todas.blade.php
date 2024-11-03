@extends('layouts.app')

@section('title', 'Ver Incidencias')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('incidencias.todas')
</div>
@endsection
