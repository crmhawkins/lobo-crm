
@extends('layouts.app')

@section('title', 'Ver Clientes')

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
    @livewire('comercial.clientes')
</div>
@endsection
