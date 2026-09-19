@extends('layouts.app')
@section('title', 'Editar paciente')
@section('subtitle', $paciente->nombre_completo)
@section('content')
    @include('pacientes.form', ['action' => route('pacientes.update', $paciente), 'method' => 'PUT'])
@endsection
