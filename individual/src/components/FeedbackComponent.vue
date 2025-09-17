<template>
  <div class="feedback-container">
    <h2 class="feedback-title">Deja tu valoración</h2>
    <form @submit.prevent="submitFeedback" class="feedback-form">
      <div class="form-group">
        <label for="stars" class="form-label">Estrellas</label>
        <select v-model="feedback.stars" required class="form-select">
          <option v-for="star in 5" :key="star" :value="star">{{ star }}</option>
        </select>
      </div>
      <div class="form-group">
        <label for="comment" class="form-label">Comentario</label>
        <textarea v-model="feedback.comment" class="form-textarea"></textarea>
      </div>
      <div class="form-group">
        <label for="images" class="form-label">Imágenes</label>
        <input type="file" @change="handleFileUpload" multiple class="form-file">
      </div>
      <button type="submit" class="submit-button">Enviar</button>
    </form>
  </div>
</template>

<script>
import { useRoute, useRouter } from 'vue-router';

export default {
  data() {
    return {
      feedback: {
        stars: 1,
        comment: '',
        images: []
      },
      token: null
    };
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    return { route, router };
  },
  mounted() {
    this.token = this.route.params.token;
  },
  methods: {
    handleFileUpload(event) {
      this.feedback.images = Array.from(event.target.files);
    },
    async submitFeedback() {
      const formData = new FormData();
      formData.append('stars', this.feedback.stars);
      formData.append('comment', this.feedback.comment);
      this.feedback.images.forEach((image, index) => {
        formData.append(`images[${index}]`, image);
      });

      try {
        const response = await fetch(`${import.meta.env.VITE_API_URL}/api/v1/feedbacks/${this.token}`, {
          method: 'PUT',
          headers: {
            'Authorization': `Bearer ${import.meta.env.VITE_API_IDENTIFIER}`
          },
          body: formData
        });

        if (!response.ok) {
          throw new Error('Error al enviar la valoración');
        }

        alert('Gracias por tu valoración');
        this.router.push('/');
      } catch (error) {
        alert(error.message);
      }
    }
  }
};
</script>

<style scoped>
.feedback-container {
  max-width: 100%;
  padding: 20px;
  margin: 0 auto;
  box-sizing: border-box;
}

.feedback-title {
  text-align: center;
  margin-bottom: 20px;
  font-size: clamp(1.5rem, 2.5vw, 2rem);
}

.feedback-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-weight: 600;
  font-size: clamp(0.9rem, 2vw, 1rem);
}

.form-select, .form-textarea, .form-file {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 16px;
  width: 100%;
  box-sizing: border-box;
}

.form-textarea {
  min-height: 100px;
  resize: vertical;
}

.form-file {
  padding: 8px;
}

.submit-button {
  padding: 12px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
  margin-top: 10px;
}

.submit-button:hover {
  background-color: #45a049;
}

@media (max-width: 768px) {
  .feedback-form {
    padding: 15px;
  }
  
  .form-select, .form-textarea, .form-file {
    padding: 8px;
  }
}

@media (max-width: 480px) {
  .feedback-container {
    padding: 10px;
  }
  
  .feedback-form {
    gap: 15px;
  }
  
  .submit-button {
    padding: 10px 15px;
  }
}
</style>