<template>
    <div class="loading-spinner">
      <div class="spinner"></div>
      <div class="loading-text">Carregant informació...</div>
    </div>
  </template>
  
  <script setup>
  // No necesitamos lógica adicional para este componente
  </script>
  
  <style scoped>
  .loading-spinner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh; /* Ocupa toda la altura de la pantalla */
    background-color: var(--background-main); /* Usa tu color de fondo */
  }
  
  .spinner {
    width: 60px;
    height: 60px;
    border: 6px solid var(--primary-color); /* Color primario de tu app */
    border-top: 6px solid transparent; /* Parte transparente para el efecto de giro */
    border-radius: 50%; /* Hace que el div sea un círculo */
    animation: spin 1s linear infinite; /* Animación de giro */
  }
  
  @keyframes spin {
    0% {
      transform: rotate(0deg); /* Inicia la rotación en 0 grados */
    }
    100% {
      transform: rotate(360deg); /* Completa la rotación en 360 grados */
    }
  }
  
  .loading-text {
    margin-top: 20px;
    font-size: 1.2rem;
    color: var(--text-primary); /* Color de texto primario de tu app */
    font-weight: 500;
  }
  </style>