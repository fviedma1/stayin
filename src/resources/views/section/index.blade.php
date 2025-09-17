@extends('layouts.master')

@section('title', 'Seccions de l\'Hotel')

@section('content')
<div class="dashboard">
    @if (session('success'))
    <div class="message-content message-content--info" id="status-message">
        {{ session('success') }}
    </div>
    @endif
    <div class="dashboard__header">
        <h1>Consulta les seccions de {{ $hotel->name }}</h1>

        <a href="{{ route('hotel.showOne', $hotel->id) }}" class="btn btn--orange">
            Tornar
        </a>
    </div>
    <p class="hotel__titleSection">Selecciona i arrossega les seccions per ordenar-les segons l'ordre en què vols que apareguin a la pàgina principal del teu hotel.</p>

    <!-- Formulari per editar les seccions -->
    <form id="edit-sections-form" action="{{ route('section.update', ['hotelId' => $hotel->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="sections-list" id="sections-list">
            @if ($sections->isEmpty())
            <p>No hi ha seccions disponibles per a aquest hotel.</p>
            @else
            <div class="sections-grid">
                @foreach ($sections as $section)
                <div class="section-card" draggable="true" data-id="{{ $section->id }}">
                    <h4>{{ $section->name }}</h4>
                    <div class="section-controls">
                        <div class="form-group">
                            <label>Visible:</label>
                            <select name="sections[{{ $section->id }}][is_visible]" class="form-control">
                                <option value="1" {{ $section->pivot->is_visible ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ !$section->pivot->is_visible ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <!-- Mostrar el camp "Mostrar (Quantitat)" o un espai reservat -->
                        @if ($section->slug !== 'room_types')
                        <div class="form-group">
                            <label>Mostrar (Quantitat):</label>
                            <input type="number" name="sections[{{ $section->id }}][display_count]"
                                value="{{ $section->pivot->display_count ?? 1 }}" min="1" class="form-control small-input">
                        </div>
                        @else
                        <div class="form-group placeholder"></div> <!-- Espai reservat -->
                        @endif
                    </div>
                    <input type="hidden" name="sections[{{ $section->id }}][order]" value="{{ $loop->index + 1 }}">
                    <input type="hidden" name="sections[{{ $section->id }}][id]" value="{{ $section->id }}">
                </div>
                @endforeach
            </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Desar canvis</button>
    </form>
</div>
@endsection