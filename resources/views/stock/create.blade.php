@extends('layouts.app')

@section('title', 'Nuevo lote de producto')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('stock.create-component', ['identificador' => $id])
</div>
@endsection


