@extends('layouts.app')

@section('title', 'Crear Presupuesto')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('presupuestos.create-component')
</div>
@endsection


