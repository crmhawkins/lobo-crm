@extends('layouts.app')

@section('title', 'Asignar mercadería por producto')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('materiales-producto.create-component', ['identificador'=>$id])
</div>
@endsection

