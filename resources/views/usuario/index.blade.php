@extends('layouts.app')

@section('title', 'Ver Usuarios')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('usuarios.index-component')
</div>

@endsection