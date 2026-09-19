@extends('layouts.app')
@section('title', 'Editar aseguradora')
@section('subtitle', $aseguradora->nombre)
@section('content')
    @include('aseguradores.form', ['action' => route('aseguradores.update', $aseguradora), 'method' => 'PUT'])
@endsection
