@extends('layouts.app')

@section('title', 'Ver productos subalmacenes')

@section('head')


    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')

<div>
    @livewire('stock-subalmacen.index-component')
</div>

 @endsection
