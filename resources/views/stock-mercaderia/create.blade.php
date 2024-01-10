@extends('layouts.app')

@section('title', 'Registrar stock de mercadería entrante')

@section('head')
@vite(['resources/sass/productos.scss'])
@vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('stock-mercaderia.create-component', ['identificador' => $id])
</div>
@endsection
