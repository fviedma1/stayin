@extends('layouts.master')

@section('content')
<div class="container">
    <div class="dashboard">
        <h1>Editar Notícia</h1>
        
        <form method="POST" action="{{ route('news.update', $news->id) }}" enctype="multipart/form-data" class="dashboard-form">
            @csrf
            @method('PUT')
            
            <div class="form__columns">
                <div class="form__column">
                    <h3 class="form__heading">Informació Bàsica</h3>
                    <div class="form-group">
                        <label for="title" class="form__label">Títol</label>
                        <input type="text" name="title" id="title" class="form__input" 
                               value="{{ old('title', $news->title) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="short_description" class="form__label">Descripció Curta</label>
                        <textarea name="short_description" id="short_description" 
                                  class="form__input" rows="3" required>{{ old('short_description', $news->short_description) }}</textarea>
                    </div>
                </div>

                <div class="form__column">
                    <h3 class="form__heading">Contingut Complet</h3>
                    <div class="form-group">
                        <label for="long_description" class="form__label">Descripció Llarga</label>
                        <textarea name="long_description" id="long_description" 
                                  class="form__input" rows="5" required>{{ old('long_description', $news->long_description) }}</textarea>
                    </div>
                </div>

                <div class="form__column">
                    <h3 class="form__heading">Configuració</h3>
                    <div class="form-group">
                        <label for="images" class="form__label">Imatges</label>
                        <input type="file" name="images[]" id="images" class="form__input" multiple>
                         <!-- Vista previa de imágenes existentes -->
                        @if($news->images)
                        <div class="image-preview">
                            @foreach($news->image_urls ?? [] as $url)
                                <img src="{{ $url }}" class="thumbnail-form">
                            @endforeach
                        </div>
                         @endif
                    </div>
                    
                    <!-- Selector de hoteles condicional -->
                    <div class="form-group">
                        <label class="form__label">Publicar immediatament?</label>
                        <div class="form__toggle">
                            <input type="radio" name="published" id="published-yes" value="1" 
                                   {{ old('published', $news->published) ? 'checked' : '' }}>
                            <label for="published-yes" class="form__toggle-label">Sí</label>
                            
                            <input type="radio" name="published" id="published-no" value="0" 
                                   {{ !old('published', $news->published) ? 'checked' : '' }}>
                            <label for="published-no" class="form__toggle-label">No</label>
                        </div>
                    </div>

                    <!-- Hoteles asociados (solo visible si se selecciona "Sí") -->
                    <div class="form-group">
                        <label for="hotels" class="form__label">Hotels Associats</label>
                        <select name="hotels[]" id="hotels" class="form__input" multiple required>
                                @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}" 
                                    {{ in_array($hotel->id, old('hotels', $news->hotels ? $news->hotels->pluck('id')->toArray() : [])) ? 'selected' : '' }}>
                                    {{ $hotel->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Botones de navegación -->
                    <div class="form__actions">
                        <a href="{{ route('news.news') }}" class="btn btn--cancel">
                            <i class="fas fa-times"></i> Cancel·lar
                        </a>
                        <button type="submit" class="btn btn--submit">
                            <i class="fas fa-save"></i> Actualitzar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .dashboard-form {
        background: var(--background-main);
        padding: 25px;
        border-radius: 10px;
        margin-top: 25px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    }
    
    .form__columns {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2rem;
    }
    
    .form__heading {
        font-size: 1.2rem;
        color: var(--primary-color);
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form__label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--text-primary);
    }
    
    .form__input {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--text-primary);
    }
    
    .form__input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px var(--primary-color-light);
    }
    
    .form__toggle {
        display: flex;
        gap: 1rem;
    }
    
    .form__toggle-label {
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        cursor: pointer;
    }
    
    .form__toggle input:checked + .form__toggle-label {
        background: var(--primary-color);
        color: white;
    }
    
    .image-preview {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .image-preview img {
        max-width: 100px;
        border-radius: 6px;
    }
    
    .form__actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        justify-content: flex-end;
    }
    
    @media (max-width: 768px) {
        .form__columns {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection