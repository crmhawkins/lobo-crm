@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('usuarios.create-component')
</div>
@endsection

