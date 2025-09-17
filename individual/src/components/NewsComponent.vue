<template>
  <section v-if="isVisible" class="news-listing-section">
    <h2 class="section-title">{{ sectionTitle }}</h2>
    
    <div v-if="loading" class="loading-container">
      <p>Carregant notícies...</p>
    </div>
    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
    </div>
    <div v-else-if="!newsItems || newsItems.length === 0" class="no-news">
      <p>No hi ha notícies disponibles en aquest moment.</p>
    </div>

    <div v-else>
      <div class="news-list-grid">
        <article v-for="item in newsItems" :key="item.id" class="news-list-card">
          <div class="news-list-image-wrapper">
            <img :src="getNewsImageUrl(item)" :alt="item.title" class="news-list-image"/>
          </div>
          <div class="news-list-content">
            <h3 class="news-list-title">{{ item.title }}</h3>
            <p class="news-list-short-desc">{{ item.short_description }}</p>
            <div class="news-list-meta">
              <span class="news-list-date">{{ formatDate(item.created_at) }}</span>
              <a v-if="item.hotels && item.hotels.length > 0" href="#" class="news-list-hotel-link">
                {{ item.hotels[0].name }}
              </a>
            </div>
            <!-- Podrías añadir un botón/enlace para leer más si tienes una vista de detalle de noticia -->
          </div>
        </article>
      </div>

      <!-- Controles de Paginación -->
      <nav v-if="paginationData && paginationData.last_page > 1" class="pagination-controls" aria-label="Navegació de notícies">
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: paginationData.current_page === 1 }">
            <button class="page-link" @click.prevent="fetchNews(paginationData.current_page - 1)" :disabled="paginationData.current_page === 1">
              Anterior
            </button>
          </li>
          <li v-for="page in paginationData.last_page" :key="page" class="page-item" :class="{ active: page === paginationData.current_page }">
            <button class="page-link" @click.prevent="fetchNews(page)">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: paginationData.current_page === paginationData.last_page }">
            <button class="page-link" @click.prevent="fetchNews(paginationData.current_page + 1)" :disabled="paginationData.current_page === paginationData.last_page">
              Següent
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';

const API_URL = import.meta.env.VITE_API_URL;
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER; // Asumo que tienes esto para el hotel actual

const newsItems = ref([]);
const loading = ref(true);
const error = ref(null);
const isVisible = ref(true); // Visibilidad por defecto, se puede ajustar con la config de sección
const sectionTitle = ref('Totes les Notícies'); // Título por defecto
const itemsPerPage = ref(10); // Valor por defecto, se actualizará desde la config
const paginationData = ref(null);

const defaultNewsImage = 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?q=80&w=1000'; // Imagen por defecto genérica

const fetchSectionConfig = async () => {
  if (!API_IDENTIFIER) {
    console.warn('API_IDENTIFIER no està definit. No es pot carregar la configuració de la secció de notícies.');
    return; // No podemos continuar sin el identificador del hotel
  }
  try {
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/sections`);
    if (!response.ok) {
      throw new Error(`Error carregant la configuració de seccions: ${response.status}`);
    }
    const sections = await response.json();
    const newsSectionConfig = sections.find(s => s.slug === 'news');

    if (newsSectionConfig && newsSectionConfig.pivot) {
      isVisible.value = Boolean(newsSectionConfig.pivot.is_visible);
      if (newsSectionConfig.pivot.display_count) {
        itemsPerPage.value = parseInt(newsSectionConfig.pivot.display_count, 10);
      }
      // Podrías usar newsSectionConfig.name para sectionTitle si está disponible
      // sectionTitle.value = newsSectionConfig.name || 'Totes les Notícies';
    }
  } catch (err) {
    console.error('Error en fetchSectionConfig:', err.message);
    // No establecemos error.value aquí para que el componente intente cargar noticias igualmente
  }
};

const fetchNews = async (page = 1) => {
  if (!API_IDENTIFIER) {
    error.value = 'Identificador de l\'hotel no disponible.';
    loading.value = false;
    return;
  }
  loading.value = true;
  error.value = null;
  try {
    // El endpoint ya usa itemsPerPage.value (displayCount) internamente para paginar
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/noticies/all?page=${page}`);
    if (!response.ok) {
      throw new Error(`Error carregant les notícies: ${response.status}`);
    }
    const data = await response.json();
    newsItems.value = data.data; // Las noticias están en la propiedad 'data'
    paginationData.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total,
    };
  } catch (err) {
    console.error('Error en fetchNews:', err.message);
    error.value = 'No s\'han pogut carregar les notícies.';
    newsItems.value = [];
    paginationData.value = null;
  } finally {
    loading.value = false;
  }
};

const getNewsImageUrl = (item) => {
  if (item.images && item.images.length > 0 && item.images[0].image) {
    return item.images[0].image;
  }
  return defaultNewsImage;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('ca-ES', options);
};

onMounted(async () => {
  await fetchSectionConfig(); // Cargar configuración primero para obtener itemsPerPage
  await fetchNews(1);       // Luego cargar la primera página de noticias
});

</script>

<style scoped>
.news-listing-section {
  padding: 20px 10px; /* Reducido para móviles */
  background-color: #f9f9f9;
  box-sizing: border-box; /* Para incluir padding en el width/height */
}

.section-title {
  font-size: 1.8rem; /* Reducido para móviles */
  color: #333;
  margin-bottom: 20px; /* Reducido */
  text-align: center;
}

.loading-container, .error-message, .no-news {
  text-align: center;
  padding: 20px; /* Reducido */
  font-size: 1rem; /* Ajustado */
  color: #555;
}

.news-list-grid {
  display: grid;
  grid-template-columns: 1fr; /* Por defecto una columna en móviles */
  gap: 20px; /* Ajustado */
  max-width: 1200px;
  margin: 0 auto;
}

.news-list-card {
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08); /* Suavizado */
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.news-list-image-wrapper {
  width: 100%;
  height: 180px; /* Ligeramente reducido para móviles */
}

.news-list-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.news-list-content {
  padding: 15px; /* Reducido */
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.news-list-title {
  font-size: 1.25rem; /* Reducido */
  color: #2c3e50;
  margin-bottom: 8px; /* Reducido */
  font-weight: 600;
}

.news-list-short-desc {
  font-size: 0.9rem; /* Reducido */
  color: #555;
  line-height: 1.5; /* Ajustado */
  margin-bottom: 12px; /* Reducido */
  flex-grow: 1;
}

.news-list-meta {
  font-size: 0.8rem; /* Reducido */
  color: #7f8c8d;
  border-top: 1px solid #ecf0f1;
  padding-top: 8px; /* Reducido */
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: center; /* Para alinear mejor si un texto es más largo */
}
.news-list-hotel-link {
  color: var(--primary-color, #3498db);
  text-decoration: none;
  white-space: nowrap; /* Evitar que el nombre del hotel se rompa en dos líneas fácilmente */
  overflow: hidden;
  text-overflow: ellipsis; /* Añadir puntos suspensivos si es muy largo */
  max-width: 150px; /* Limitar el ancho para evitar que empuje la fecha */
}
.news-list-hotel-link:hover {
  text-decoration: underline;
}

/* Estilos de Paginación */
.pagination-controls {
  display: flex;
  justify-content: center;
  margin-top: 30px; /* Reducido */
  padding: 0 10px; /* Espacio para que no toque los bordes en móvil */
}

.pagination {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
  border-radius: 4px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08); /* Suavizado */
  overflow-x: auto; /* Permitir scroll horizontal si hay muchos números de página */
  -webkit-overflow-scrolling: touch; /* Scroll suave en iOS */
  max-width: 100%; /* Asegurar que no exceda el contenedor */
}

.page-item {
  margin: 0;
  flex-shrink: 0; /* Evitar que los items se encojan demasiado */
}

.page-link {
  display: block;
  padding: 8px 12px; /* Reducido para móviles */
  background-color: #fff;
  color: var(--primary-color, #3498db);
  border: 1px solid #ddd;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s;
  text-decoration: none;
  font-size: 0.9rem; /* Ligeramente reducido */
}

.page-item:first-child .page-link {
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.page-item:last-child .page-link {
  border-top-right-radius: 4px;
  border-bottom-right-radius: 4px;
}

.page-item + .page-item .page-link {
  border-left: none;
}

.page-item.active .page-link {
  background-color: var(--primary-color, #3498db);
  color: white;
  border-color: var(--primary-color, #3498db);
  z-index: 1;
}

.page-item.disabled .page-link {
  color: #aaa;
  cursor: not-allowed;
  background-color: #f9f9f9;
}

.page-link:hover:not(:disabled):not(.active) { /* Corrección: active no debe tener hover */
  background-color: #f0f0f0;
}

/* Tablet Styles (a partir de 600px) */
@media (min-width: 600px) {
  .news-listing-section {
    padding: 30px 15px;
  }

  .section-title {
    font-size: 2rem;
    margin-bottom: 25px;
  }

  .news-list-grid {
    /* Se mantiene el auto-fill, pero podemos ajustar el minmax si es necesario */
    /* Por ejemplo, para asegurar al menos dos columnas si hay espacio */
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Ajuste ligero de minmax */
    gap: 20px;
  }
  
  .news-list-image-wrapper {
    height: 200px;
  }

  .news-list-content {
    padding: 18px;
  }

  .news-list-title {
    font-size: 1.3rem;
  }

  .news-list-short-desc {
    font-size: 0.92rem;
  }

  .news-list-meta {
    font-size: 0.82rem;
    padding-top: 10px;
  }
  
  .pagination {
    overflow-x: visible; /* En tablet ya no debería ser necesario el scroll */
  }
  
  .page-link {
    padding: 10px 15px;
    font-size: 1rem;
  }
}

/* Desktop Styles (a partir de 992px) */
@media (min-width: 992px) {
  .news-listing-section {
    padding: 40px 20px;
  }

  .section-title {
    font-size: 2.2rem; /* Original era 2rem, ligeramente aumentado */
    margin-bottom: 30px;
  }
  
  .news-list-grid {
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); /* Minmax aumentado para desktop */
    gap: 25px; /* Original */
  }

  .news-list-card:hover { /* Efecto hover para escritorio */
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.12);
    transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
  }
  
  .news-list-title {
    font-size: 1.4rem; /* Original */
  }

  .news-list-short-desc {
    font-size: 0.95rem; /* Original */
  }

  .news-list-meta {
    font-size: 0.85rem; /* Original */
  }
   .news-list-hotel-link {
    max-width: none; /* Quitar la limitación en desktop si se prefiere */
  }
}

</style>
