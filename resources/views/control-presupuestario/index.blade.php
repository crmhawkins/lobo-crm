
@extends('layouts.app')

@section('title', 'Control Presupuestario')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
    <style>
        ul.pagination{
            justify-content: center;
        }

        </style>
@endsection

@section('content-principal')
<div>
    @livewire('control-presupuestario.index-component')
</div>
@endsection
