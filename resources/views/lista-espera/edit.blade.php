@extends('layouts.app')
@section('title', 'Editar registro de espera')
@section('subtitle', $registro->paciente->nombre_completo ?? '')
@section('content')
    @include('lista-espera.form', ['action' => route('lista-espera.update', $registro), 'method' => 'PUT'])
@endsection
