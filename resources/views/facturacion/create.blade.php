@extends('layouts.app')
@section('title', 'Nueva factura')
@section('subtitle', 'Registrar factura')
@section('content')
    @include('facturacion.form', ['action' => route('facturacion.store'), 'method' => 'POST'])
@endsection
