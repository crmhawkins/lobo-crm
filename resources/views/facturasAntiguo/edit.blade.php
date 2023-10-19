@extends('layouts.app')

@section('content')

<div>
    @livewire('facturas.edit-component', ['identificador'=>$id])
</div>

@endsection