
@extends('layouts.app')

@section('title', 'Editar Servicio Pack')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios-packs.edit-component', ['identificador'=>$id])
</div>
@endsection


