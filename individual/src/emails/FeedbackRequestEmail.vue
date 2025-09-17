<template>
  <div class="email-container">
    <div class="email-header">
      <img src="logo.png" alt="Hotel Logo" class="logo">
      <h1>¡Gracias por tu estancia!</h1>
    </div>

    <div class="email-content">
      <p>Estimado/a {{ userName }},</p>
      
      <p>Esperamos que hayas disfrutado de tu estancia en {{ hotelName }}. Nos encantaría conocer tu opinión para seguir mejorando nuestros servicios.</p>
      
      <div class="cta-container">
        <a :href="feedbackUrl" class="feedback-button">
          Compartir mi experiencia
        </a>
      </div>
      
      <p class="note">Este enlace es único y expirará en 7 días.</p>
    </div>

    <div class="email-footer">
      <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
      <div class="social-links">
        <a href="#" class="social-link">Facebook</a>
        <a href="#" class="social-link">Instagram</a>
        <a href="#" class="social-link">Twitter</a>
      </div>
    </div>
  </div>
</template>

<style>
.email-container {
  max-width: 600px;
  margin: 0 auto;
  font-family: Arial, sans-serif;
  color: #333;
  background: #ffffff;
}

.email-header {
  text-align: center;
  padding: 2rem;
  background: var(--primary-color);
  color: white;
}

.logo {
  max-width: 150px;
  margin-bottom: 1rem;
}

.email-content {
  padding: 2rem;
  line-height: 1.6;
}

.cta-container {
  text-align: center;
  margin: 2rem 0;
}

.feedback-button {
  display: inline-block;
  padding: 1rem 2rem;
  background: var(--primary-color);
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: bold;
}

.note {
  font-size: 0.9rem;
  color: #666;
  text-align: center;
  margin-top: 1rem;
}

.email-footer {
  padding: 2rem;
  text-align: center;
  background: #f8f9fa;
  border-top: 1px solid #eee;
}

.social-links {
  margin-top: 1rem;
}

.social-link {
  color: var(--primary-color);
  text-decoration: none;
  margin: 0 0.5rem;
}
</style>

<script setup>
defineProps({
  userName: {
    type: String,
    required: true
  },
  hotelName: {
    type: String,
    required: true
  },
  feedbackUrl: {
    type: String,
    required: true
  }
});
</script> 