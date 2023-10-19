
@extends('layouts.app')

@section('title', 'Editar Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('eventos.edit-component', ['identificador'=>$id])
</div>
@endsection