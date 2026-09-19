@extends('layouts.app')
@section('title', 'Editar usuario')
@section('subtitle', $usuario->name)
@section('content')
    @include('usuarios.form', ['action' => route('usuarios.update', $usuario), 'method' => 'PUT'])
@endsection
