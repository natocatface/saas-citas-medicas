@extends('layouts.app')
@section('title', 'Nuevo usuario')
@section('subtitle', 'Crear cuenta de acceso')
@section('content')
    @include('usuarios.form', ['action' => route('usuarios.store'), 'method' => 'POST'])
@endsection
