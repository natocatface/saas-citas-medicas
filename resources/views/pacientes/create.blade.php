@extends('layouts.app')
@section('title', 'Nuevo paciente')
@section('subtitle', 'Registrar un paciente')
@section('content')
    @include('pacientes.form', ['action' => route('pacientes.store'), 'method' => 'POST'])
@endsection
