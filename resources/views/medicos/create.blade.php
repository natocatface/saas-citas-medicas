@extends('layouts.app')
@section('title', 'Nuevo médico')
@section('subtitle', 'Registrar personal médico')
@section('content')
    @include('medicos.form', ['action' => route('medicos.store'), 'method' => 'POST'])
@endsection
