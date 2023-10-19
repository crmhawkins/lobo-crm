@extends('layouts.app')

@section('title', 'Crear Departamento')

@section('head')
@vite(['resources/sass/productos.scss'])
@vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('programas.create-component')
</div>

@endsection