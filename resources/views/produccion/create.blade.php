@extends('layouts.app')

@section('title', 'Crear órden de producción')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('produccion.create-component')
</div>
@endsection


