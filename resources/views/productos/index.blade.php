@extends('layouts.app')

@section('title', 'Ver productos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
    <style>
        ul.pagination {
            justify-content: center;
        }
    </style>
@endsection
@section('content-principal')

    <div>
        @livewire('productos.index-component')
    </div>

@endsection
