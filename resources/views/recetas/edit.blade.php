@extends('layouts.app')
@section('title', 'Editar receta')
@section('subtitle', $receta->numero)
@section('content')
    @include('recetas.form', ['action' => route('recetas.update', $receta), 'method' => 'PUT'])
@endsection
