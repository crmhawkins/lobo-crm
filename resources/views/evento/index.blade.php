@extends('layouts.app')

@section('title', 'Eventos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('eventos.index-component')
</div>

@endsection