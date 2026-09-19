@extends('layouts.app')
@section('title', 'Nueva clínica')
@section('subtitle', 'Registrar clínica en la plataforma')
@section('content')
    @include('saas.clinicas.form', ['action' => route('saas.clinicas.store'), 'method' => 'POST'])
@endsection
