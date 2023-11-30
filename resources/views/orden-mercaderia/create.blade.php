@extends('layouts.app')

@section('title', 'Crear órden de compra de mercadería')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('orden-mercaderia.create-component')
</div>
@endsection


