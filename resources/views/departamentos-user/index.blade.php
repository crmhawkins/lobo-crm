
@extends('layouts.app')

@section('title', 'Ver Departamentos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('departamentos-user.index-component')
</div>
@endsection