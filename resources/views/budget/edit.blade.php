@extends('layouts.app')

@section('content')

@livewire('budget-component', ['response' => $response])

@endsection 