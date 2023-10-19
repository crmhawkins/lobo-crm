@extends('layouts.app')

@section('title', 'Editar Articulo')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('articulos.edit-component', ['identificador' => $id])
</div>
@endsection
