@extends('layouts.app')
@section('title', 'Editar diagnóstico')
@section('subtitle', $item->codigo)
@section('content')
    @include('cie10.form', ['action' => route('cie10.update', $item), 'method' => 'PUT'])
@endsection
