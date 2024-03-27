@extends('layouts.app')

@section('title', 'Traspaso de stock')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('stock.traspaso-component', ['identificador'=>$id])
</div>

@endsection

