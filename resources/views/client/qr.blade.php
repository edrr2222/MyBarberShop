@php $barberia = auth('client')->user()->barberia; @endphp
@extends('layouts.app', ['barberia' => $barberia])

@section('header-actions')
    <form method="POST" action="{{ route('client.logout') }}">
        @csrf
        <button type="submit" class="btn-secundario" style="margin:0;color:#fff;border-color:#fff">Salir</button>
    </form>
@endsection

@section('content')
<div class="card" style="text-align:center">
    <h1>Hola, {{ auth('client')->user()->nombre }}</h1>
    <p style="color:#666">Muestra este código al barbero para sumar tu sello</p>
    <div id="client-qr-app"></div>
</div>
@endsection
