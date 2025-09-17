@extends('layouts.master')

@section('content')
<div class="container">
    <div class="dashboard">
        <h1>Crear Nova Notícia</h1>

        <form method="POST" action="{{ route('news.store') }}" enctype="multipart/form-data" class="dashboard-form">
            @csrf
            
            <!-- Información Básica -->
            <div class="form-step form-step--active" data-step="1">
                <h3 class="form__heading">Informació Bàsica</h3>
                <div class="form-group">
                    <label for="title" class="form__label">Títol</label>
                    <input type="text" name="title" id="title" class="form__input" 
                           value="{{ old('title') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="short_description" class="form__label">Descripció Curta</label>
                    <textarea name="short_description" id="short_description" 
                              class="form__input" rows="3" required>{{ old('short_description') }}</textarea>
                </div>
            </div>

            <!-- Contenido Completo -->
            <div class="form-step" data-step="2">
                <h3 class="form__heading">Contingut Complet</h3>
                <div class="form-group">
                    <label for="long_description" class="form__label">Descripció Llarga</label>
                    <textarea name="long_description" id="long_description" 
                              class="form__input" rows="5" required>{{ old('long_description') }}</textarea>
                </div>
            </div>

            <!-- Configuración -->
            <div class="form-step" data-step="3">
                <h3 class="form__heading">Configuració</h3>
                <div class="form-group">
                    <label for="images" class="form__label">Imatges</label>
                    <input type="file" name="images[]" id="images" class="form__input" multiple>
                    <!-- Vista previa de imágenes -->
                    <div id="image-preview" class="image-preview"></div>
                </div>
                
                <!-- Selector de hoteles condicional -->
                <div class="form-group">
                    <label class="form__label">Publicar immediatament?</label>
                    <div class="form__toggle">
                        <input type="radio" name="published" id="published-yes" value="1" 
                               {{ old('published', false) ? 'checked' : '' }}>
                        <label for="published-yes" class="form__toggle-label">Sí</label>
                        
                        <input type="radio" name="published" id="published-no" value="0" 
                               {{ !old('published', false) ? 'checked' : '' }}>
                        <label for="published-no" class="form__toggle-label">No</label>
                    </div>
                </div>

                <!-- Hoteles asociados (solo visible si se selecciona "Sí") -->
                <div id="hotels-section" class="form-group" style="display: none;">
                    <label for="hotels" class="form__label">Hotels Associats</label>
                    <select name="hotels[]" id="hotels" class="form__input" multiple>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}" {{ in_array($hotel->id, old('hotels', [])) ? 'selected' : '' }}>
                                {{ $hotel->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Botones de navegación -->
            <div class="form__actions">
                <button type="button" class="btn btn--cancel form__btn--prev">
                    <i class="fas fa-arrow-left"></i> Anterior
                </button>
                <button type="button" class="btn btn--submit form__btn--next">
                    Següent <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn btn--submit form__btn--submit" style="display: none;">
                    <i class="fas fa-save"></i> Desar
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .dashboard-form {
        padding: 25px;
        border-radius: 10px;
        margin-top: 25px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    }
    
    .form-step {
        display: none;
    }
    
    .form-step--active {
        display: block;
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const steps = document.querySelectorAll('.form-step');
        const prevBtn = document.querySelector('.form__btn--prev');
        const nextBtn = document.querySelector('.form__btn--next');
        const submitBtn = document.querySelector('.form__btn--submit');
        let currentStep = 1;

        // Mostrar el primer paso al cargar la página
        document.querySelector('.form-step[data-step="1"]').classList.add('form-step--active');

        nextBtn.addEventListener('click', () => {
            if (currentStep < 3) {
                document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('form-step--active');
                currentStep++;
                document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('form-step--active');
                if (currentStep === 3) {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'inline-block';
                }
                prevBtn.style.display = 'inline-block';
            }
        });

        prevBtn.addEventListener('click', () => {
            if (currentStep > 1) {
                document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('form-step--active');
                currentStep--;
                document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.add('form-step--active');
                nextBtn.style.display = 'inline-block';
                submitBtn.style.display = 'none';
                if (currentStep === 1) {
                    prevBtn.style.display = 'none';
                }
            }
        });

        // Mostrar/ocultar hoteles según la selección de "Sí" o "No"
        const publishedYes = document.getElementById('published-yes');
        const publishedNo = document.getElementById('published-no');
        const hotelsSection = document.getElementById('hotels-section');

        publishedYes.addEventListener('change', () => {
            hotelsSection.style.display = 'block';
        });

        publishedNo.addEventListener('change', () => {
            hotelsSection.style.display = 'none';
        });

        // Vista previa de imágenes
        const imageInput = document.getElementById('images');
        const imagePreview = document.getElementById('image-preview');

        imageInput.addEventListener('change', () => {
            imagePreview.innerHTML = '';
            Array.from(imageInput.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    imagePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
@endsection