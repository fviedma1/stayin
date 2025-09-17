<template>
  <section v-if="isVisible" class="news-section" :style="{ '--section-order': sectionOrder }">
    <h2 class="section-title">Noticies i Novetats</h2>
    <div v-if="loading" class="loading-container">
      <p>Carregant notícies...</p>
    </div>
    <div v-else-if="error" class="error-message">
      <p>Error: {{ error }}</p>
    </div>
    <div v-else-if="displayNews.length === 0" class="no-news">
      <p>No hi ha notícies disponibles.</p>
    </div>
    <div v-else class="news-grid">
      <div 
        v-for="(item, index) in displayNews" 
        :key="item.id" 
        class="news-card"
      >
        <div class="news-image">
          <img 
            :src="getNewsImage(item)"
            :alt="item.title"
          />
        </div>
        <div class="news-content">
          <h3 class="news-title">{{ item.title }}</h3>
          <p class="news-description">{{ item.short_description }}</p>
          <div v-if="item.hotels && item.hotels.length > 0" class="news-hotels">
          </div>
          <div class="news-meta">
            <div class="meta-item">
              <div class="meta-label">Data</div>
              <div class="meta-value">{{ formatDate(item.created_at) }}</div>
            </div>
            <div class="meta-item">
              <div class="meta-label">Tags</div>
              <div class="meta-value">{{ item.tags || 'General' }}</div>
            </div>
            <div class="meta-item">
              <div class="meta-label">Usuari</div>
              <div class="meta-value">{{ item.user_id || 'Admin' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const API_URL = import.meta.env.VITE_API_URL;
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER;

const news = ref([]);
const loading = ref(true);
const error = ref(null);
const isVisible = ref(true);
const sectionOrder = ref(0);
const displayLimit = ref(null);

const defaultHotelImages = [
  'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1000', // Fachada hotel
  'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1000', // Habitación de lujo
  'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=1000', // Piscina hotel
  'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?q=80&w=1000', // Recepción
  'https://images.unsplash.com/photo-1590381105924-c72589b9ef3f?q=80&w=1000'  // Restaurante hotel
];

const getRandomDefaultImage = () => {
  const randomIndex = Math.floor(Math.random() * defaultHotelImages.length);
  return defaultHotelImages[randomIndex];
};

// Función original de formato fecha
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('ca-ES', { day: 'numeric', month: 'long', year: 'numeric' });
};

const loadConfig = async () => {
  try {
    console.log('Cargando configuración de secciones...');
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/sections`);
    if (!response.ok) throw new Error('Error al cargar la configuración');
    
    const sections = await response.json();
    console.log('Secciones recibidas:', sections);
    
    const newsSection = sections.find(s => s.slug === 'news');
    console.log('Configuración de noticias:', newsSection);
    
    if (newsSection?.pivot) {
      isVisible.value = Boolean(newsSection.pivot.is_visible);
      sectionOrder.value = parseInt(newsSection.pivot.order) || 0;
      displayLimit.value = parseInt(newsSection.pivot.display_count) || null;
      
      console.log('Configuración aplicada:', {
        isVisible: isVisible.value,
        sectionOrder: sectionOrder.value,
        displayLimit: displayLimit.value
      });
    }
  } catch (err) {
    console.error('Error cargando configuración:', err);
  }
};

const fetchNews = async () => {
  try {
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/noticies`);
    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }
    const data = await response.json();
    news.value = Array.isArray(data) ? data : [];
    console.log('Noticias cargadas:', news.value.length);
    console.log('Límite de visualización:', displayLimit.value);
  } catch (err) {
    error.value = "Error carregant les notícies";
    console.error('Error en fetchNews:', err);
  } finally {
    loading.value = false;
  }
};

const displayNews = computed(() => {
  // Ya no necesitamos filtrar por published aquí porque viene filtrado del backend
  return news.value.slice(0, 3);
});

const getNewsImage = (newsItem) => {
  if (newsItem.images && newsItem.images.length > 0) {
    return newsItem.images[0].image; // Accedemos al campo image del objeto ImagesNews
  }
  return getRandomDefaultImage();
};

onMounted(async () => {
  try {
    // 1. Cargar noticias primero
    await fetchNews()
    // 2. Cargar configuración de forma independiente
    loadConfig()
  } catch (error) {
    console.error('Error en la carga inicial:', error)
  }
});
</script>

<style scoped>
.news-section {
  padding: 30px 0; /* Móvil: Reducido */
  background-color: #ffffff;
  box-sizing: border-box;
}

.section-title {
  font-size: 28px; /* Móvil: Reducido */
  color: var(--primary-color);
  margin-bottom: 30px; /* Móvil: Reducido */
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
}

.news-grid {
  display: grid;
  grid-template-columns: 1fr; /* Por defecto una columna en móviles */
  gap: 25px; /* Móvil: Reducido */
  max-width: 500px; /* Limitar ancho en móvil para una mejor lectura de tarjeta única */
  margin: 0 auto;
  padding: 0 15px; /* Móvil: Reducido y consistente */
}

.news-card {
  background: #ffffff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Suavizado */
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
}

.news-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
}

.news-image {
  position: relative;
  width: 100%;
  height: 220px; /* Móvil: Altura de imagen ajustada */
}

.news-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.news-content {
  padding: 20px; /* Móvil: Reducido */
  display: flex;
  flex-direction: column;
  flex-grow: 1; /* Para que el contenido ocupe espacio y meta se vaya abajo */
}

.news-title {
  font-size: 20px; /* Móvil: Reducido */
  color: var(--primary-color);
  margin-bottom: 10px; /* Móvil: Reducido */
  font-weight: bold;
}

.news-description {
  font-size: 15px; /* Móvil: Ajustado */
  color: #333333;
  line-height: 1.5;
  margin-bottom: 15px; /* Móvil: Reducido */
  flex-grow: 1; /* Para que la descripción ocupe espacio si es corta */
}

.news-hotels {
  margin-bottom: 15px; /* Móvil: Reducido */
}

.news-meta {
  display: grid;
  grid-template-columns: 1fr; /* Móvil: Una columna para meta items */
  gap: 10px; /* Móvil: Reducido */
  padding-top: 15px; /* Móvil: Reducido */
  border-top: 1px solid #eee;
  margin-top: auto; /* Empuja la meta-info al final de la tarjeta */
}

.meta-item {
  text-align: left; /* Móvil: Alinear a la izquierda para mejor lectura en una columna */
}

.meta-label {
  font-size: 13px; /* Móvil: Reducido */
  color: var(--primary-color);
  font-weight: bold;
  margin-bottom: 3px; /* Móvil: Reducido */
  text-transform: uppercase;
}

.meta-value {
  font-size: 13px; /* Móvil: Reducido */
  color: #333333;
}

/* Tablet Styles */
@media (min-width: 768px) {
  .news-section {
    padding: 45px 0;
  }
  .section-title {
    font-size: 32px;
    margin-bottom: 45px;
  }
  .news-grid {
    /* Para 3 noticias, 2 en una fila y 1 en la siguiente, o ajustar con minmax */
    /* Si siempre son 3, podemos hacer algo como: */
    /* grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); */
    /* O mantener el control más explícito que ya estaba: */
    grid-template-columns: repeat(2, 1fr);
    max-width: 800px; /* Ajustar para 2 tarjetas */
    padding: 0 30px;
  }
  .news-grid > .news-card:nth-child(odd):last-child { /* Si hay un número impar de tarjetas (ej. 3), la última ocupa todo el ancho */
    grid-column: span 2; 
  }

  .news-image {
    height: 250px;
  }
  .news-content {
    padding: 25px;
  }
  .news-title {
    font-size: 22px;
  }
  .news-description {
    font-size: 15px;
  }
  .news-meta {
    grid-template-columns: repeat(3, 1fr); /* Volver a 3 columnas para meta en tablet */
    gap: 15px;
  }
  .meta-item {
    text-align: center; /* Centrar meta items en tablet/desktop */
  }
}

/* Desktop Styles */
@media (min-width: 1024px) {
  .news-section {
    padding: 60px 0;
  }
  .section-title {
    font-size: 36px;
    margin-bottom: 60px;
  }
  .news-grid {
    grid-template-columns: repeat(3, 1fr); /* 3 tarjetas en una fila */
    max-width: 1200px; /* Ajustar para 3 tarjetas más grandes */
    padding: 0 40px;
  }
  .news-grid > .news-card:nth-child(odd):last-child { /* Resetear el span si venía de tablet */
    grid-column: auto;
  }
  .news-image {
    height: 280px; /* Ligeramente más grande para desktop */
  }
}

/* Estilos para mensajes de carga, error, no-news */
.loading-container,
.error-message,
.no-news {
  text-align: center;
  padding: 30px 20px; /* Móvil */
  font-size: 16px;   /* Móvil */
  background: #f9f9f9; /* Fondo ligeramente diferente para destacar */
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  margin: 20px auto; /* Centrar y dar espacio */
  max-width: 90%;   /* Evitar que ocupe todo el ancho en pantallas muy pequeñas */
}

@media (min-width: 768px) {
  .loading-container,
  .error-message,
  .no-news {
    padding: 40px;
    font-size: 18px;
    max-width: 600px; /* Limitar ancho en pantallas más grandes */
  }
}

.error-message {
  color: #dc3545;
  background-color: #f8d7da; /* Fondo rojo claro para errores */
  border: 1px solid #f5c6cb;
}

.no-news {
  color: #666666;
}
</style>