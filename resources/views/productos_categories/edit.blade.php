@extends('layouts.app')

@section('content')

@section('title', 'Productos - Categorías')


<div>
    @livewire('productoscategories.edit-component', ['identificador'=>$id])
</div>

@endsection

