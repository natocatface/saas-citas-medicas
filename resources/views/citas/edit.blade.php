@extends('layouts.app')
@section('title', 'Editar cita')
@section('subtitle', 'Modificar datos de la cita')
@section('content')
    @include('citas.form', ['action' => route('citas.update', $cita), 'method' => 'PUT'])
@endsection
