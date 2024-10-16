@extends('layouts.app')

@section('title', 'REGISTRAR STOCK SUBALMACEN')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('stock-subalmacen.create-component', ['identificador' => $id])
</div>
@endsection


