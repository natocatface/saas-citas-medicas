@extends('layouts.app')
@section('title', 'Nuevo diagnóstico CIE-10')
@section('subtitle', 'Agregar al catálogo')
@section('content')
    @include('cie10.form', ['action' => route('cie10.store'), 'method' => 'POST'])
@endsection
