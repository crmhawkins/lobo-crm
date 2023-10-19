@extends('layouts.app')

@section('title', 'Editando producto')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('productos.edit-component', ['identificador'=>$id])
</div>

@endsection

