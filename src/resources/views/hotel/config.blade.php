@extends('layouts.master')

@section('title', 'Configuració de l\'hotel')

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <i class="bi bi-gear-wide-connected"></i>
            {{ $hotel->name }}
        </h1>
    </div>

    <form action="{{ route('hotel.config.update', $hotel->id) }}" method="POST" class="dashboard-form">
        @csrf
        @method('PUT')

        <div class="glass-card sections-table">
            <div class="table-header">
                <div class="table-column">Secció</div>
                <div class="table-column">Visibilitat</div>
                <div class="table-column">Ordre</div>
            </div>
            
            <div class="table-body">
                @foreach (['Habitacions', 'Noticies', 'Feedbacks'] as $section)
                @php
                    $visibility = $config->sections_visibility[$section] ?? 1;
                    $order = $config->sections_order[$section] ?? 1;
                @endphp
                <div class="table-row">
                    <div class="table-column section-name">{{ $section }}</div>
                    
                    <div class="table-column visibility-control">
                        <div class="toggle-group">
                            <label class="toggle-btn {{ $visibility ? 'active' : '' }}" data-color="success">
                                <input type="radio" 
                                       name="sections_visibility[{{ $section }}]" 
                                       value="1" 
                                       {{ $visibility ? 'checked' : '' }}>
                                Sí
                            </label>
                            <label class="toggle-btn {{ !$visibility ? 'active' : '' }}" data-color="error">
                                <input type="radio" 
                                       name="sections_visibility[{{ $section }}]" 
                                       value="0" 
                                       {{ !$visibility ? 'checked' : '' }}>
                                No
                            </label>
                        </div>
                    </div>
                    
                    <div class="table-column order-control">
                        <input type="number" 
                               name="sections_order[{{ $section }}]" 
                               value="{{ $order }}" 
                               min="1"
                               class="order-input">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="glass-card limits-grid">
            <div class="limit-item">
                <label for="news_limit">
                    <i class="bi bi-newspaper"></i>
                    Límit de notícies
                </label>
                <input type="number" 
                       name="news_limit" 
                       id="news_limit" 
                       value="{{ old('news_limit', $config->news_limit) }}">
            </div>
            
            <div class="limit-item">
                <label for="comments_limit">
                    <i class="bi bi-chat-dots"></i>
                    Límit de comentaris
                </label>
                <input type="number" 
                       name="comments_limit" 
                       id="comments_limit" 
                       value="{{ old('comments_limit', $config->comments_limit) }}">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="save-btn">
                <i class="bi bi-cloud-upload-fill"></i>
                Guardar canvis
            </button>
        </div>
    </form>
</div>

<style>
:root {
    --background-main: #121212;
    --background-light: #2C2F33;
    --primary-color: #cc7a39;
    --secundary-color: #FFA462;
    --text-primary: #f4f4f4;
    --text-secondary: #F2F2F5;
    --border-color: #343A40;
    --hover-color: #2C2F33;
    --status-success: #06C270;
    --status-success-hover: #06a25f;
    --status-error: #c20606;
    --status-error-hover: #a20606;
    --status-warning: #FFCC00;
    --status-warning-hover: #FFB300;
    --status-info: #066ac2; 
    --status-info-hover: #0654a2;
}

.dashboard-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: var(--background-main);
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    color: var(--text-primary);
}
.dashboard-title {
    font-size: 2rem;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
}
.glass-card {
    background: var(--background-light);
    border: 1px solid var(--border-color);
    border-radius: 0.8rem;
    padding: 2rem;
    margin-bottom: 2rem;
}
.table-header {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    background: var(--primary-color);
    color: var(--text-primary);
    padding: 1rem;
    border-radius: 0.8rem;
}
.table-row {
    display: flex;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid var(--border-color);
}
.toggle-group {
    display: flex;
    gap: 1rem;
}
.save-btn {
    padding: 1.2rem 3rem;
    background: var(--primary-color);
    color: var(--text-primary);
    border-radius: 0.8rem;
    cursor: pointer;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.order-input').forEach(input => {
        input.addEventListener('change', function() {
            const container = this.closest('.table-body');
            const rows = Array.from(container.querySelectorAll('.table-row'));
            
            rows.sort((a, b) => {
                return a.querySelector('.order-input').value - b.querySelector('.order-input').value;
            });

            rows.forEach(row => container.appendChild(row));
        });
    });
});
</script>
@endsection