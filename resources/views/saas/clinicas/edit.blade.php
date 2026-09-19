@extends('layouts.app')
@section('title', 'Editar clínica')
@section('subtitle', $clinica->nombre)
@section('content')
    @include('saas.clinicas.form', ['action' => route('saas.clinicas.update', $clinica), 'method' => 'PUT'])
@endsection
