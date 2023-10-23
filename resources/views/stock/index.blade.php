@extends('layouts.app')

@section('title', 'Ver lotes de productos')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')

<div>
    @livewire('stock.index-component')
</div>

 @endsection
