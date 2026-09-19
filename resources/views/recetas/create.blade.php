@extends('layouts.app')
@section('title', 'Nueva receta')
@section('subtitle', 'Emitir prescripción médica')
@section('content')
    @include('recetas.form', ['action' => route('recetas.store'), 'method' => 'POST'])
@endsection
