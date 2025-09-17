<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
  <div class="app">
    <main class="sections-container">
      <router-view></router-view>
    </main>
  </div>
</template>

<style>
:root {
  --primary-color: #cc7a39;
  --secondary-color: #FFA462;
  --text-primary: #333333;
  --text-secondary: #666666;
  
  /* Variables para tamaños de fuente */
  --font-size-xl: 32px;
  --font-size-lg: 24px;
  --font-size-md: 18px;
  --font-size-base: 16px;
  --font-size-sm: 14px;
  
  /* Variables para espaciado */
  --spacing-xl: 60px;
  --spacing-lg: 40px;
  --spacing-md: 20px;
  --spacing-sm: 10px;
}

.sections-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* Aseguramos que las secciones ocupen el ancho completo */
.sections-container > section {
  width: 100%;
}

/* Estilos generales para títulos de sección */
.section-title {
  font-size: var(--font-size-xl);
  color: var(--primary-color);
  text-align: center;
  font-weight: bold;
  margin-bottom: var(--spacing-lg);
  text-transform: uppercase;
}

/* Aplicamos el orden directamente */
.habitacio-view,
.news-section,
.feedback-section {
  order: var(--section-order);
  padding: var(--spacing-xl) 0;
}

/* Estilos para el sistema de orden de secciones */
section {
  display: block;
  width: 100%;
}

/* Aseguramos que las secciones mantengan su espacio */
section + section {
  margin-top: 0;
}

@media (max-width: 768px) {
  :root {
    --font-size-xl: 28px;
    --font-size-lg: 22px;
    --font-size-md: 16px;
    --spacing-xl: 40px;
    --spacing-lg: 30px;
  }
}
</style>

