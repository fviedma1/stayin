@extends('layouts.master')

@section('title', 'Descripció de l\'hotel')

@section('content')

    <div class="dashboard">
        <div class="hotel-description">
            <h2>Nom del hotel: {{ $hotel->name }}</h2>
            <p>Adreça: {{ $hotel->address }}</p>
            <p>Ciutat: {{ $hotel->city }}</p>
            <p>Pais: {{ $hotel->country }}</p>
            <p>Telefon: {{ $hotel->telephone }}</p>
            <p>Correu electrònic: {{ $hotel->email }}</p>
        </div>
    </div>

@endsection
