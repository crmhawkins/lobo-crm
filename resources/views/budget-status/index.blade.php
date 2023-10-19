@extends('layouts.app')

@section('content')

@livewire('budget-status-component', ['response' => $budgetStatus])

@endsection 