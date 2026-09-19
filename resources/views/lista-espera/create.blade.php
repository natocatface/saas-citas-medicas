@extends('layouts.app')
@section('title', 'Agregar a lista de espera')
@section('subtitle', 'Nuevo registro en espera')
@section('content')
    @include('lista-espera.form', ['action' => route('lista-espera.store'), 'method' => 'POST'])
@endsection
