@extends('layouts.app')

@section('title', 'Crear Tipo de Gasto')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('tipo-gasto.create-component')
</div>
@endsection