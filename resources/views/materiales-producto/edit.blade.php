@extends('layouts.app')

@section('title', 'Editar asignar mercadería por producto')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('materiales-producto.edit-component', ['identificador'=>$id])
</div>
@endsection

