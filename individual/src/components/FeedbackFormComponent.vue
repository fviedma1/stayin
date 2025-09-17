<template>
  <div class="feedback-form-container">
    <div v-if="loading" class="loading">
      <i class="fas fa-spinner fa-spin"></i> Carregant...
    </div>
    
    <div v-else-if="error" class="error-message">
      {{ error }}
    </div>
    
    <div v-else-if="submitted" class="success-message">
      <i class="fas fa-check-circle"></i>
      <h2>Gràcies per la teva opinió!</h2>
      <p>La teva valoració ens ajuda a millorar.</p>
    </div>
    
    <form v-else @submit.prevent="submitReview" class="feedback-form">
      <h2>La teva opinió és important</h2>
      
      <div class="form-group">
        <label>Valoració</label>
        <div class="stars-container">
          <button 
            v-for="star in 5" 
            :key="star"
            type="button"
            class="star-button"
            :class="{ active: star <= stars }"
            @click="stars = star"
          >
            <i class="fas fa-star"></i>
          </button>
        </div>
      </div>

      <div class="form-group">
        <label for="comment">Comentari</label>
        <textarea 
          id="comment"
          v-model="comment"
          rows="4"
          required
          placeholder="Explica'ns la teva experiència..."
        ></textarea>
      </div>

      <div class="form-group">
        <label for="images">Imatges (opcional, màxim 3)</label>
        <input 
          type="file" 
          id="images" 
          @change="handleImageUpload" 
          multiple 
          accept="image/jpeg, image/png, image/gif"
          class="file-input"
        />
        <div v-if="imagePreviews.length > 0" class="image-previews">
          <div v-for="(src, index) in imagePreviews" :key="index" class="preview-item">
            <img :src="src" alt="Previsualització" class="preview-image"/>
            <button type="button" @click="removeImage(index)" class="remove-image-btn">&times;</button>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button 
          type="submit" 
          class="submit-button"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting">
            <i class="fas fa-spinner fa-spin"></i> Enviant...
          </span>
          <span v-else>Enviar valoració</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const API_URL = import.meta.env.VITE_API_URL;

// Estado del componente
const loading = ref(true);
const error = ref(null);
const submitted = ref(false);
const isSubmitting = ref(false);

// Datos del formulario
const stars = ref(0);
const comment = ref('');
const token = ref('');
const reserveId = ref(null);
const selectedImages = ref([]); // Para almacenar los File objects
const imagePreviews = ref([]); // Para almacenar las URLs de previsualización

// Validar token y cargar datos
onMounted(async () => {
  try {
    token.value = route.params.token;
    if (!token.value) {
      throw new Error('Token no proporcionado en la URL');
    }

    const response = await fetch(`${API_URL}/v2/verify-token/${token.value}`);
    const data = await response.json();

    if (!data.success) {
      throw new Error(data.message || 'Token inválido o expirado');
    }

    reserveId.value = data.reserve_id;
    loading.value = false;
  } catch (err) {
    console.error('Error validando token:', err);
    error.value = err.message;
    loading.value = false;
  }
});

const MAX_IMAGES = 3;
const handleImageUpload = (event) => {
  const files = Array.from(event.target.files);
  error.value = null; // Limpiar errores previos

  if (selectedImages.value.length + files.length > MAX_IMAGES) {
    error.value = `Només pots pujar un màxim de ${MAX_IMAGES} imatges.`;
    event.target.value = null; // Limpiar el input de archivo
    return;
  }

  files.forEach(file => {
    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        error.value = 'Format d\'imatge no vàlid. Només JPG, PNG, GIF.';
        event.target.value = null;
        return;
    }
    if (file.size > 2 * 1024 * 1024) { // 2MB
        error.value = 'La imatge és massa gran (màxim 2MB).';
        event.target.value = null;
        return;
    }

    selectedImages.value.push(file);
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreviews.value.push(e.target.result);
    };
    reader.readAsDataURL(file);
  });
  event.target.value = null; // Limpiar para permitir seleccionar los mismos archivos si se eliminan y re-seleccionan
};

const removeImage = (index) => {
  selectedImages.value.splice(index, 1);
  imagePreviews.value.splice(index, 1);
};

// Enviar review
const submitReview = async () => {
  if (stars.value === 0) {
    error.value = 'Por favor, selecciona una valoración';
    return;
  }
  error.value = null; // Limpiar errores previos
  isSubmitting.value = true;
  
  const formData = new FormData();
  formData.append('stars', stars.value);
  formData.append('comment', comment.value);
  // No incluimos reserve_id aquí, el backend lo debería saber por el token

  selectedImages.value.forEach((imageFile) => {
    formData.append('images[]', imageFile); // 'images[]' para que PHP lo reciba como un array
  });

  try {
    // Endpoint necesita ser capaz de manejar FormData (multipart/form-data)
    const response = await fetch(`${API_URL}/v2/submit-feedback/${token.value}`, {
      method: 'POST',
      // No 'Content-Type': 'application/json' aquí, FormData lo maneja
      body: formData 
    });

    const data = await response.json(); // Asumiendo que el backend responde con JSON

    if (!data.success) {
      throw new Error(data.message || 'Error al enviar la valoración');
    }

    submitted.value = true;
  } catch (err) {
    error.value = err.message;
    console.error('Error enviando review:', err);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
.feedback-form-container {
  max-width: 100%;
  width: 100%;
  padding: 1.5rem;
  margin: 0 auto;
  box-sizing: border-box;
}

.loading,
.error-message,
.success-message {
  text-align: center;
  padding: 1.5rem;
  font-size: clamp(1rem, 2vw, 1.2rem);
}

.success-message {
  color: #155724;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
  border-radius: 8px;
  margin: 1rem 0;
}

.success-message i {
  font-size: 3rem;
  color: #28a745;
  margin-bottom: 1rem;
}

.error-message {
  color: #721c24;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-size: clamp(0.9rem, 2vw, 1.1rem);
}

.feedback-form {
  max-width: 600px;
  margin: 0 auto;
  padding: 1.5rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.feedback-form h2 {
  text-align: center;
  color: var(--primary-color);
  margin-bottom: 1.5rem;
  font-size: clamp(1.3rem, 3vw, 1.8rem);
}

.form-group {
  margin-bottom: 1.25rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #333;
  font-size: clamp(0.95rem, 2vw, 1.05rem);
}

.stars-container {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-bottom: 1rem;
}

.star-button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: clamp(1.8rem, 5vw, 2.5rem);
  color: #ccc;
  transition: color 0.2s ease, transform 0.2s ease;
  padding: 0.2rem;
}

.star-button:hover {
  transform: scale(1.1);
}

.star-button.active {
  color: #ffd700;
}

textarea {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  resize: vertical;
  min-height: 120px;
  font-family: inherit;
  font-size: clamp(0.9rem, 2vw, 1rem);
}

.file-input {
  display: block;
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-size: clamp(0.9rem, 2vw, 1rem);
}

.image-previews {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
  margin-top: 0.8rem;
}

.preview-item {
  position: relative;
  border: 1px solid #ddd;
  padding: 4px;
  border-radius: 4px;
}

.preview-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
}

.remove-image-btn {
  position: absolute;
  top: -8px;
  right: -8px;
  background: rgba(0,0,0,0.7);
  color: white;
  border: none;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  font-size: 12px;
  line-height: 22px;
  text-align: center;
  cursor: pointer;
}

.form-actions {
  margin-top: 1.5rem;
}

.submit-button {
  width: 100%;
  padding: 0.9rem;
  background: var(--primary-color);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: clamp(1rem, 2vw, 1.1rem);
  cursor: pointer;
  transition: background-color 0.3s ease, opacity 0.3s ease;
}

.submit-button:hover:not(:disabled) {
  background: var(--secondary-color);
  opacity: 0.9;
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .feedback-form-container {
    padding: 1rem;
  }
  
  .feedback-form {
    padding: 1.2rem;
  }
  
  .star-button {
    font-size: clamp(1.5rem, 6vw, 2rem);
  }
  
  .preview-image {
    width: 70px;
    height: 70px;
  }
}

@media (max-width: 480px) {
  .feedback-form-container {
    padding: 0.8rem;
  }
  
  .feedback-form {
    padding: 1rem;
  }
  
  .stars-container {
    gap: 0.3rem;
  }
  
  .preview-image {
    width: 60px;
    height: 60px;
  }
  
  .submit-button {
    padding: 0.8rem;
  }
}
</style>