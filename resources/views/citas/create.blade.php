@extends('layouts.app')
@section('title', 'Nueva cita')
@section('subtitle', 'Agendar una cita médica')
@section('content')
    @include('citas.form', ['action' => route('citas.store'), 'method' => 'POST'])
@endsection
