
@extends('layouts.app')

@section('title', 'Gestión de pedidos en Almacén')
@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.css">
    @vite(['resources/sass/productos.scss'])
    <style>
div#datatable-buttons_enviados_filter{
            display: flex !important;
            flex-direction: row-reverse !important;
            align-items: center !important;
        }
        div#datatable-buttons_preparacion_filter{
            display: flex !important;
            flex-direction: row-reverse !important;
            align-items: center !important;
        }
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
        div#Botonesfiltros-preparacion {
            padding: 5px;
        }
        div#Botonesfiltros-enviados {
            padding: 5px;
        }
        div#Botonesfiltros {
            padding: 5px;
        }
        </style>
@endsection

@section('content-principal')
<div>
    @livewire('almacen.index-component')
</div>

@endsection
