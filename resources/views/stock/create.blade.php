@extends('layouts.app')

@section('title', 'REGISTRAR ENTRADA DE STOCK DE MERCADERÍA')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('stock.create-component', ['identificador' => $id])
</div>
@endsection


