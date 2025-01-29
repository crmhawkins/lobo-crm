@extends('layouts.app')

@section('title', 'Ver Cuadro de Giro Bancario')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('operaciones.giro')
</div>




@endsection
