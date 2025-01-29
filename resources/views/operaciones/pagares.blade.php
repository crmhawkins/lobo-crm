@extends('layouts.app')

@section('title', 'Ver Pagares')


@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('operaciones.pagares')
</div>



@endsection
