@extends('layouts.app')
@section('title', 'Editar especialidad')
@section('subtitle', $especialidad->nombre)
@section('content')
    @include('especialidades.form', ['action' => route('especialidades.update', $especialidad), 'method' => 'PUT'])
@endsection
