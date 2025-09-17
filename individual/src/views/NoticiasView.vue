<template>
  <div class="noticias-view">
    <HeaderFixComponent />
    
    <main class="noticias-content-vue">
      <section class="news-section-vue">
        <h1 class="section-title-vue">Notícies i Novetats</h1>
        
        <div v-if="loading" class="loading-container-vue">
          <p>Carregant notícies...</p>
        </div>
        
        <div v-else-if="error" class="error-message-vue">
          <p>Error: {{ error }}</p>
        </div>
        
        <div v-else-if="!newsItems || newsItems.length === 0" class="no-news-vue">
          <p>No hi ha notícies disponibles.</p>
        </div>
        
        <div v-else class="news-grid-vue">
          <div 
            v-for="(item) in newsItems" 
            :key="item.id" 
            class="news-card-vue"
          >
            <div class="news-image-vue">
              <img 
                :src="getNewsImageUrl(item)" 
                :alt="item.title"
              />
            </div>
            <div class="news-card-content-vue">
              <h2 class="news-card-title-vue">{{ item.title }}</h2>
              <p class="news-card-description-vue">{{ item.short_description }}</p>
              <div class="news-card-meta-vue">
                <div class="meta-item-vue">
                  <div class="meta-label-vue">Data</div>
                  <div class="meta-value-vue">{{ formatDate(item.created_at) }}</div>
                </div>
                <div class="meta-item-vue" v-if="item.hotels && item.hotels.length > 0">
                  <div class="meta-label-vue">Hotel</div>
                  <div class="meta-value-vue">{{ item.hotels[0].name }}</div>
                </div>
                <!-- Puedes añadir más meta datos si es necesario -->
              </div>
            </div>
          </div>
        </div>

        <!-- Paginación -->
        <nav v-if="paginationData && paginationData.last_page > 1" class="pagination-controls-vue" aria-label="Navegació de notícies">
          <ul class="pagination-vue">
            <li class="page-item-vue" :class="{ disabled: paginationData.current_page === 1 }">
              <button class="page-link-vue" @click.prevent="fetchNews(paginationData.current_page - 1)" :disabled="paginationData.current_page === 1">
                Anterior
              </button>
            </li>
            <li v-for="pageNumber in paginationData.last_page" :key="pageNumber" 
                class="page-item-vue" 
                :class="{ active: pageNumber === paginationData.current_page }">
              <button class="page-link-vue" @click.prevent="fetchNews(pageNumber)">{{ pageNumber }}</button>
            </li>
            <li class="page-item-vue" :class="{ disabled: paginationData.current_page === paginationData.last_page }">
              <button class="page-link-vue" @click.prevent="fetchNews(paginationData.current_page + 1)" :disabled="paginationData.current_page === paginationData.last_page">
                Següent
              </button>
            </li>
          </ul>
        </nav>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import HeaderFixComponent from '@/components/HeaderFixComponent.vue';

const router = useRouter();
const API_URL = import.meta.env.VITE_API_URL;
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER;

const newsItems = ref([]); // Renombrado de news a newsItems para claridad
const loading = ref(true);
const error = ref(null);
const paginationData = ref(null); // Para almacenar datos de paginación del backend
// itemsPerPage se obtendrá implícitamente de paginationData.per_page si es necesario mostrarlo

const defaultHotelImages = [
  'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=1000',
  'https://images.unsplash.com/photo-1582719508461-905c673771fd?q=80&w=1000',
  // ... más imágenes por defecto
];

const getRandomDefaultImage = () => {
  const randomIndex = Math.floor(Math.random() * defaultHotelImages.length);
  return defaultHotelImages[randomIndex];
};

const getNewsImageUrl = (item) => {
  if (item.images && item.images.length > 0 && item.images[0].image) {
    return item.images[0].image;
  }
  return getRandomDefaultImage(); // Asegúrate que getRandomDefaultImage esté definida o usa una URL fija
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('ca-ES', { day: 'numeric', month: 'long', year: 'numeric' });
};

// loadConfig ya no es estrictamente necesario para itemsPerPage si el backend lo maneja,
// pero podría ser útil para la visibilidad de la sección u otros datos.
// Por ahora, lo mantendremos si lo usas para otras cosas, pero la paginación no dependerá de su `itemsPerPage.value`
const loadConfig = async () => {
  try {
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/sections`);
    if (!response.ok) throw new Error('Error al cargar la configuración de secciones');
    const sections = await response.json();
    const newsSection = sections.find(s => s.slug === 'news');
    // Aquí podrías usar newsSection.pivot.is_visible si quieres controlar la visibilidad de toda la sección
    // if (newsSection && newsSection.pivot) { isVisible.value = Boolean(newsSection.pivot.is_visible); }
  } catch (err) {
    console.error('Error cargando configuración de secciones:', err);
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
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/noticies/all?page=${page}`);
    if (!response.ok) {
      throw new Error(`Error HTTP carregant les notícies: ${response.status}`);
    }
    const data = await response.json();
    newsItems.value = data.data; // Las noticias paginadas están en la propiedad 'data'
    paginationData.value = {
      current_page: data.current_page,
      last_page: data.last_page,
      per_page: data.per_page,
      total: data.total,
      from: data.from,
      to: data.to,
      links: data.links // Útil para construir paginación más compleja si se desea
    };
  } catch (err) {
    console.error('Error en fetchNews:', err.message);
    error.value = "Error carregant les notícies";
    newsItems.value = [];
    paginationData.value = null;
  } finally {
    loading.value = false;
  }
};

const updateMetaTags = () => {
  document.title = 'Notícies - Hotel Nicolau Copèrnic'; 
  
  let metaDescription = document.querySelector('meta[name="description"]');
  if (!metaDescription) {
    metaDescription = document.createElement('meta');
    metaDescription.name = 'description';
    document.head.appendChild(metaDescription);
  }
  metaDescription.content = 'Descobreix les últimes notícies i novetats de l\'Hotel Nicolau Copèrnic.'; 

  const updateMetaTag = (property, content) => {
    let meta = document.querySelector(`meta[property="${property}"]`);
    if (!meta) {
      meta = document.createElement('meta');
      meta.setAttribute('property', property);
      document.head.appendChild(meta);
    }
    meta.content = content;
  };

  updateMetaTag('og:title', 'Notícies - Hotel Nicolau Copèrnic');
  updateMetaTag('og:description', 'Descobreix les últimes notícies i novetats de l\'Hotel Nicolau Copèrnic.');
  // ... más metatags
};

onMounted(async () => {
  updateMetaTags();
  await loadConfig(); // Cargar configuración general de la sección (ej. visibilidad)
  await fetchNews(1); // Cargar la primera página de noticias
});

watch(() => router.currentRoute.value.query.page, (newPage) => {
  const pageNum = parseInt(newPage, 10) || 1;
  fetchNews(pageNum);
}, { immediate: true }); // `immediate: true` si quieres que se ejecute al montar también por query param

// También podrías querer actualizar la URL con el número de página
watch(() => paginationData.value?.current_page, (newCurrentPage) => {
  if (newCurrentPage && router.currentRoute.value.query.page !== newCurrentPage.toString()) {
    router.push({ query: { ...router.currentRoute.value.query, page: newCurrentPage } });
  }
});

</script>

<style scoped>
/* He añadido -vue al final de cada clase para evitar colisiones si tienes estilos globales con los mismos nombres */
.noticias-view {
  padding-top: 80px; 
}

.noticias-content-vue {
  max-width: 1400px;
  margin: 0 auto;
  padding: 40px 20px;
}

.news-section-vue {
  background-color: #ffffff;
}

.section-title-vue {
  font-size: 36px;
  color: var(--primary-color);
  margin-bottom: 60px;
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
}

.news-grid-vue {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 40px;
  margin-bottom: 40px;
}

.news-card-vue {
  background: #ffffff;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  transition: transform 0.3s ease;
  display: flex; /* Para mejor alineación interna si es necesario */
  flex-direction: column;
}

.news-card-vue:hover {
  transform: translateY(-5px);
}

.news-image-vue {
  position: relative;
  width: 100%;
  height: 200px;
}

.news-image-vue img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.news-card-content-vue {
  padding: 20px;
  flex-grow: 1; /* Hace que esta parte crezca para empujar el pie de página hacia abajo */
  display: flex;
  flex-direction: column;
}

.news-card-title-vue {
  font-size: 1.5rem; /* Ajustado para consistencia */
  color: var(--primary-color);
  margin-bottom: 10px;
  font-weight: bold;
}

.news-card-description-vue {
  font-size: 1rem;
  color: #333333;
  line-height: 1.6;
  margin-bottom: 15px;
  flex-grow: 1; /* Permite que la descripción crezca */
}

.news-card-meta-vue {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); /* Más adaptable */
  gap: 15px;
  padding-top: 15px;
  border-top: 1px solid #eee;
  margin-top: auto; /* Empuja al final de la tarjeta */
}

.meta-item-vue {
  text-align: center;
}

.meta-label-vue {
  font-size: 0.8rem; /* Ajustado */
  color: var(--primary-color);
  font-weight: bold;
  margin-bottom: 5px;
  text-transform: uppercase;
}

.meta-value-vue {
  font-size: 0.9rem; /* Ajustado */
  color: #333333;
}

/* Estilos de Paginación Nuevos y Mejorados */
.pagination-controls-vue {
  display: flex;
  justify-content: center;
  margin-top: 40px;
  padding-bottom: 20px;
}

.pagination-vue {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  overflow: hidden; /* Para asegurar que los bordes redondeados se apliquen a los hijos */
}

.page-item-vue {
  margin: 0;
}

.page-link-vue {
  display: block;
  padding: 10px 18px;
  background-color: #fff;
  color: var(--primary-color, #007bff);
  border: 1px solid #dee2e6;
  border-left-width: 0; /* Evitar doble borde */
  cursor: pointer;
  transition: background-color 0.2s, color 0.2s;
  text-decoration: none;
  font-size: 0.95rem;
}

.page-item-vue:first-child .page-link-vue {
  border-left-width: 1px; /* Borde izquierdo para el primer elemento */
}

.page-item-vue.active .page-link-vue {
  background-color: var(--primary-color, #007bff);
  color: white;
  border-color: var(--primary-color, #007bff);
  z-index: 1; /* Para que esté por encima de los otros */
}

.page-item-vue.disabled .page-link-vue {
  color: #6c757d;
  cursor: not-allowed;
  background-color: #e9ecef;
  border-color: #dee2e6;
}

.page-link-vue:hover:not([disabled]):not(.active) {
  background-color: #e9ecef;
  color: var(--primary-color-dark, #0056b3);
}

/* Otros estilos */
.loading-container-vue,
.error-message-vue,
.no-news-vue {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  margin: 20px;
}

.error-message-vue {
  color: #dc3545;
}

.no-news-vue {
  color: #666666;
}

@media (max-width: 768px) {
  .section-title-vue {
    font-size: 28px;
    margin-bottom: 40px;
  }
  .news-grid-vue {
    grid-template-columns: 1fr;
  }
  .news-card-meta-vue {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  .page-link-vue {
    padding: 8px 12px;
    font-size: 0.9rem;
  }
}
</style> 