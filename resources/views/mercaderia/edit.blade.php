@extends('layouts.app')

@section('title', 'Editar mercadería')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('mercaderia.edit-component', ['identificador'=>$id])
</div>

@endsection

