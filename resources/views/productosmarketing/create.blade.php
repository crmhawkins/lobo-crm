@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('productosmarketing.create-component')
</div>
@endsection


