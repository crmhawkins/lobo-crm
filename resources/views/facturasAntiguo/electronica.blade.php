@extends('layouts.app')

@section('content')

<div>
    @livewire('facturas.electronica-component', ['identificador'=>$id])
</div>

@endsection