@extends('layouts.app')

@section('title', 'Orden de producción')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')

<div>
    @livewire('produccion.edit-component', ['identificador' => $id])
</div>

 @endsection
