@extends('layouts.app')

@section('title', 'Ver/Editar lote de producto')

@section('head')
    {{-- @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss']) --}}

@section('content-principal')
<div>
    @livewire('stock.edit-component', ['identificador'=>$id])
</div>

@endsection

