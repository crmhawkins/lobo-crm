
@extends('layouts.app')

@section('title', 'Todas las Facturas')
@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.css">
    @vite(['resources/sass/productos.scss'])

    <style>
        div#datatable-buttons_filter {
            display: flex !important;
            flex-direction: row-reverse !important;
            align-items: center !important;
        }
        label {
            margin-top: -13px;
        }

        .row{

            margin-bottom: 5px;
        }
        div#Botonesfiltros {
            padding: 5px;
        }
        </style>
@endsection

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('facturas.index-component')
</div>

@endsection
