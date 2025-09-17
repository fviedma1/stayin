<!-- resources/views/hotel/showOne.blade.php -->
@extends('layouts.master')

@section('title', 'Detalls de l\'hotel')

@section('content')
    <div class="dashboard">
        <div class="container-card">
            <div class="container-card__main-title">
                Cerca d'hotels creats
            </div>
            <!-- Barra de búsqueda -->
            <div class="container-card__search-bar">
                <input class="container-card__search-input" type="text" id="search-input" placeholder="Cercar hotels..." />
            </div>
            <div class="cards">
                @foreach ($hotels as $hotel)
                    <x-hotel-component :hotel="$hotel" />
                @endforeach
            </div>
        </div>
    </div>
    <script src="{{ asset('js/cercador.js') }}"></script>
@endsection
