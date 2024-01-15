@extends('layouts.app')

@section('title', 'Crear Proveedores')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('proveedores.create-component')
</div>
@endsection
