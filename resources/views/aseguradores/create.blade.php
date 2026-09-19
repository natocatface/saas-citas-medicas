@extends('layouts.app')
@section('title', 'Nueva aseguradora')
@section('subtitle', 'Registrar compañía aseguradora')
@section('content')
    @include('aseguradores.form', ['action' => route('aseguradores.store'), 'method' => 'POST'])
@endsection
