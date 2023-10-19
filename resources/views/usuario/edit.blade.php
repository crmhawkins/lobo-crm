@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('usuarios.edit-component', ['identificador'=>$id])
</div>

@endsection

