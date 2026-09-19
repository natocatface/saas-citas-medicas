@extends('layouts.app')
@section('title', 'Nueva plantilla de email')
@section('subtitle', 'Crear plantilla de correo')
@section('content')
    @include('plantillas-email.form', ['action' => route('plantillas-email.store'), 'method' => 'POST'])
@endsection
