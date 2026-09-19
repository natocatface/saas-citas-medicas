@extends('layouts.app')
@section('title', 'Editar plantilla')
@section('subtitle', $plantilla->nombre)
@section('content')
    @include('plantillas-email.form', ['action' => route('plantillas-email.update', $plantilla), 'method' => 'PUT'])
@endsection
