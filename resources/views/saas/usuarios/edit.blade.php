@extends('layouts.app')
@section('title', 'Editar usuario')
@section('subtitle', $usuario->name)
@section('content')
    @include('saas.usuarios.form', ['action' => route('saas.usuarios.update', $usuario), 'method' => 'PUT'])
@endsection
