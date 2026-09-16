@php $barberia = auth('client')->user()->barberia; @endphp
@extends('layouts.app', ['barberia' => $barberia])

@section('header-actions')
    <nav style="display:flex;gap:18px;align-items:center">
        <a href="{{ route('client.qr') }}" style="opacity:1;font-weight:600">Mi QR</a>
        <a href="{{ route('client.servicios') }}" style="opacity:.85">Servicios</a>
        <form method="POST" action="{{ route('client.logout') }}">
            @csrf
            <button type="submit" class="btn-secundario" style="margin:0;color:#fff;border-color:#fff">Salir</button>
        </form>
    </nav>
@endsection

@section('content')
<div class="card" style="text-align:center">
    <h1>Hola, {{ auth('client')->user()->nombre }}</h1>
    <p style="color:#666">Muestra este código al barbero para sumar tu sello</p>
    <div id="client-qr-app"></div>
</div>
@endsection
