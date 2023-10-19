
@extends('layouts.app')

@section('title', 'Crear Servicio Pack')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios-packs.create-component')
</div>
@endsection

