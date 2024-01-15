
@extends('layouts.app')

@section('title', 'Ver Proveedores')

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
    @livewire('proveedores.index-component')
</div>
@endsection
