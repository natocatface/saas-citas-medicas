@extends('layouts.app')
@section('title', 'Nuevo plan')
@section('subtitle', 'Crear plan de suscripción')
@section('content')
    @include('saas.planes.form', ['action' => route('saas.planes.store'), 'method' => 'POST'])
@endsection
