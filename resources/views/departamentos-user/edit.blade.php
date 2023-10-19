
@extends('layouts.app')

@section('title', 'Editar Departamento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('departamentos-user.edit-component', ['identificador'=>$id])
</div>
@endsection