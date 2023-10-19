@extends('layouts.app')

@section('title', 'Editar Monitor')

@section('head')
@vite(['resources/sass/productos.scss'])
@vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('monitores.edit-component', ['identificador'=>$id])
</div>

@endsection