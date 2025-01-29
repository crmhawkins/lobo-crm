@extends('layouts.app')

@section('title', 'Cuadro de flujo')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('caja.cuadro')
</div>
@endsection
