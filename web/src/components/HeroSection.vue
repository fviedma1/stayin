<template>
  <section class="hero" data-view-transition>
    <video class="hero-video" autoplay muted loop>
      <source src="/public/videos/landing.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
    <div class="overlay"></div> <!-- Superposición oscura -->
    <div class="hero-text">
      <h1>{{ $t('heroTitle') }}</h1>
      <p class="custom-paragraph">{{ $t('heroSubtitle') }}</p>
      <RouterLink to="/search/hotels" class="button">{{ $t('heroButton') }}</RouterLink>
    </div>
  </section>
</template>

<script setup>
</script>

<style scoped>
.hero {
  position: relative;
  text-align: center;
  color: var(--background-main);
  height: 100vh;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-video {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: -1; /* Envía el video al fondo */
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: black;
  opacity: 0.4;
  z-index: 0; /* Coloca la superposición entre el video y el texto */
}

.hero-text {
  position: absolute; /* Hace que el texto se superponga sobre el video */
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%); /* Centra el contenido */
  text-align: center;
  color: var(--background-main); /* Asegura contraste sobre el video */
  z-index: 1; /* Coloca el texto por encima del video y la superposición */
  width: 80%; /* Evita que el texto se expanda demasiado */
}

.custom-paragraph {
  color: var(--background-main); /* Ajusta el color del texto */
}

.button {
  display: inline-block;
  padding: 10px 20px;
  background-color: rgba(0, 0, 0, 0.5);
  color: var(--background-main);
  text-decoration: none;
  border-radius: 5px;
}
</style>