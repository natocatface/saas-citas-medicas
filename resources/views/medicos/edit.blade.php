@extends('layouts.app')
@section('title', 'Editar médico')
@section('subtitle', $medico->nombre_completo)
@section('content')
    @include('medicos.form', ['action' => route('medicos.update', $medico), 'method' => 'PUT'])
@endsection
