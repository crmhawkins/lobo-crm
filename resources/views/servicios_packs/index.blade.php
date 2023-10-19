@extends('layouts.app')

@section('title', 'Ver Pack de Servicios')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios-packs.index-component')
</div>
@endsection