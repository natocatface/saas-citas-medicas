@extends('layouts.app')
@section('title', 'Nueva especialidad')
@section('subtitle', 'Registrar una especialidad médica')
@section('content')
    @include('especialidades.form', ['action' => route('especialidades.store'), 'method' => 'POST'])
@endsection
