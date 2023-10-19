@extends('layouts.app')

@section('title', 'Crear Monitor')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('monitores.create-component')
</div>
@endsection
