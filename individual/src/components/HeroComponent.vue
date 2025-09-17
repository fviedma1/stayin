<!-- filepath: /c:/Users/viedm/OneDrive/Documentos/GIT/grup-07-fernando-viedma/individual/src/components/HeroComponent.vue -->
<template>
  <section class="hero" data-view-transition>
    <div class="hero-image">
      <!-- Imagen de fondo -->
      <img src="../../public/images/hotel_hall.jpg" alt="Imagen de fondo" class="hero-img" />
    </div>
    <div class="overlay"></div> <!-- Superposición oscura -->
    <div class="hero-content">
      <h1 class="hero-title">Bienvenido a Nuestro Hotel</h1>
      <p class="hero-subtitle">Disfruta de una estancia inolvidable con nosotros.</p>
      <SearchBarComponent @search="handleSearch" class="search-bar" />
    </div>
  </section>
</template>

<script setup>
import SearchBarComponent from './SearchBarComponent.vue';

const handleSearch = (searchParams) => {
  console.log('Search parameters:', searchParams);
  // Aquí puedes manejar la búsqueda, por ejemplo, redirigir a una página de resultados
};
</script>

<style scoped>
:root {
  --background-main: #ffffff;
  --background-light: #f8f9fa;
  --primary-color: #cc7a39;
  --secondary-color: #FFA462;
  --text-primary: #212529;
  --text-secondary: #495057;
  --border-color: #dee2e6;
  --hover-color: #e9ecef;
}

.hero {
  position: relative;
  height: 60vh;
  min-height: 300px;
  max-height: 800px;
  width: 100%;
  overflow: hidden;
  margin-top: 3.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.hero-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.hero-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.hero-content {
  position: relative;
  z-index: 2;
  width: 90%;
  max-width: 1200px;
  text-align: center;
  padding: 1rem;
  color: var(--background-main);
}

.hero-title {
  font-size: clamp(1.8rem, 5vw, 3.5rem);
  font-weight: 700;
  margin-bottom: 1rem;
  line-height: 1.2;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
  font-size: clamp(1rem, 2.5vw, 1.5rem);
  max-width: 600px;
  margin: 0 auto 2rem;
  line-height: 1.5;
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.search-bar {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4);
  z-index: 1;
}

/* Small devices (landscape phones, 576px and up) */
@media (min-width: 36em) {
  .hero {
    height: 65vh;
    min-height: 400px;
  }
  
  .hero-content {
    width: 85%;
  }
}

/* Medium devices (tablets, 768px and up) */
@media (min-width: 48em) {
  .hero {
    height: 70vh;
    margin-top: 4rem;
  }
  
  .hero-content {
    padding: 2rem;
  }
  
  .hero-title {
    margin-bottom: 1.5rem;
  }
}

/* Large devices (desktops, 992px and up) */
@media (min-width: 62em) {
  .hero {
    height: 80vh;
  }
  
  .hero-content {
    width: 80%;
  }
}

/* Extra large devices (large desktops, 1200px and up) */
@media (min-width: 75em) {
  .hero {
    max-height: 900px;
  }
  
  .hero-title {
    margin-bottom: 2rem;
  }
}

/* XXL devices (larger desktops, 1400px and up) */
@media (min-width: 87.5em) {
  .hero {
    max-height: 1000px;
  }
}
</style>