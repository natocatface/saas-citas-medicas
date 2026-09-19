@extends('layouts.app')
@section('title', 'Editar plan')
@section('subtitle', $plan->nombre)
@section('content')
    @include('saas.planes.form', ['action' => route('saas.planes.update', $plan), 'method' => 'PUT'])
@endsection
