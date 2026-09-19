@extends('layouts.app')
@section('title', 'Nuevo usuario')
@section('subtitle', 'Crear cuenta en la plataforma')
@section('content')
    @include('saas.usuarios.form', ['action' => route('saas.usuarios.store'), 'method' => 'POST'])
@endsection
