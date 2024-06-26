
@extends('layouts.app')

@section('title', 'Todas las Facturas')
@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.css">
    @vite(['resources/sass/productos.scss'])
@endsection

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
@if (isset($id))
    <div>
        @livewire('facturas.create-rectificativa', ['idpedido'=>$id])
    </div>
@else
@livewire('facturas.create-rectificativa')
@endif


@endsection
