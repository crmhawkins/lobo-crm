@extends('layouts.app')

@section('title', 'IVA')
@section('content')

<div>
    @livewire('iva.edit-component', ['identificador'=>$id])
</div>

@endsection

