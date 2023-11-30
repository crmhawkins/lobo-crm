@extends('layouts.app')

@section('title', 'Ver órdenes de producción')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')

<div>
    @livewire('produccion.index-component')
</div>

 @endsection
