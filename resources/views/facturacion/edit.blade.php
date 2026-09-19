@extends('layouts.app')
@section('title', 'Editar factura')
@section('subtitle', $factura->numero)
@section('content')
    @include('facturacion.form', ['action' => route('facturacion.update', $factura), 'method' => 'PUT'])
@endsection
