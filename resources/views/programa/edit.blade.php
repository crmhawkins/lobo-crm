@extends('layouts.app')

@section('content')

@section('title', 'Empresas')


<div>
    @livewire('programas.edit-component', ['identificador'=>$id])
</div>

@endsection

