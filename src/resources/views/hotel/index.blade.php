@extends('layouts.master')

@section('title', 'Página de Inici')

@section('content')
    <div class="dashboard">
        <div class="dashboard__header">
            <h1>Crea el teu hotel aqui</h1>
            <a href="{{ route('hotel.create') }}" class="btn btn--orange">
                Crear ara
            </a>
        </div>
        <div class="content__below">
            <h3>Troba aquí els teus hotels</h3>
            <div class="cards">
                @foreach ($hotels as $hotel)
                    <x-hotel-component :hotel="$hotel" />
                @endforeach
            </div>
        </div>
    </div>
    <!-- Mostrar el missatge d'éxit -->
    @if (session('success'))
        <div class="message-content message-content--info" id="status-message">
            {{ session('success') }}
        </div>
    @endif

@endsection
