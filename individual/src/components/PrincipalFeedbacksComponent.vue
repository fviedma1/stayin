<template>
  <section v-if="isVisible" class="feedback-section" :style="{ '--section-order': sectionOrder }">
    <h2 class="section-title">Opinions dels nostres clients</h2>
    
    <div v-if="loading" class="loading-container">
      <p>Carregant opinions...</p>
    </div>
    
    <div v-else-if="error" class="error-message">
      <p>{{ error }}</p>
    </div>
    
    <div v-else-if="allFeedbacks.length === 0" class="no-feedbacks">
      <p>Encara no hi ha opinions</p>
    </div>
    
    <div v-else class="feedback-outer-container">
      <div class="feedback-container" @scroll="handleScroll">
        <div class="feedback-track">
          <div v-for="(feedback, index) in displayedFeedbacks" 
               :key="`${feedback.reserve_id}-${index}`"
               class="feedback-card">
            <div class="feedback-content">
              <p class="feedback-text">{{ feedback.comment }}</p>
              
              <div class="feedback-info">
                <p class="feedback-user">{{ feedback.user }}</p>
                <p class="feedback-room">{{ feedback.room }}</p>
                <p class="feedback-date">{{ formatDate(feedback.date_out) }}</p>
              </div>
            </div>
            <div class="rating">
              <span 
                v-for="star in 5" 
                :key="star" 
                class="star"
                :class="{ 'active': star <= feedback.stars }"
              >★</span>
            </div>
          </div>
          <div v-if="displayedFeedbacks.length < allFeedbacks.length" 
               class="loading-indicator">
            <span>→</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const API_URL = import.meta.env.VITE_API_URL;
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER;

const allFeedbacks = ref([]); // Almacena todos los feedbacks
const displayedFeedbacks = ref([]); // Almacena solo los feedbacks mostrados
const loading = ref(true);
const error = ref(null);
const isVisible = ref(true);
const sectionOrder = ref(0);
const ITEMS_PER_PAGE = 5;

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('ca-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const loadMoreFeedbacks = () => {
  const currentLength = displayedFeedbacks.value.length;
  const nextItems = allFeedbacks.value.slice(currentLength, currentLength + ITEMS_PER_PAGE);
  if (nextItems.length > 0) {
    displayedFeedbacks.value = [...displayedFeedbacks.value, ...nextItems];
  }
};

const handleScroll = (event) => {
  const container = event.target;
  const scrollPosition = container.scrollLeft;
  const maxScroll = container.scrollWidth - container.clientWidth;
  
  // Si estamos cerca del final (últimos 20%), cargamos más
  if (maxScroll > 0 && (maxScroll - scrollPosition) / maxScroll < 0.2) {
    loadMoreFeedbacks();
  }
};

const loadConfig = async () => {
  try {
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/sections`);
    if (!response.ok) throw new Error('Error al cargar la configuración');
    
    const sections = await response.json();
    const feedbackSection = sections.find(s => s.slug === 'feedbacks');
    
    if (feedbackSection?.pivot) {
      isVisible.value = Boolean(feedbackSection.pivot.is_visible);
      sectionOrder.value = parseInt(feedbackSection.pivot.order) || 0;
    }
  } catch (err) {
    console.error('Error cargando configuración:', err);
  }
};

const fetchFeedbacks = async () => {
  try {
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/feedbacks`);
    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }
    const data = await response.json();
    allFeedbacks.value = Array.isArray(data) ? data : [];
    // Cargamos solo los primeros 5 feedbacks inicialmente
    displayedFeedbacks.value = allFeedbacks.value.slice(0, ITEMS_PER_PAGE);
  } catch (err) {
    error.value = "Error carregant les opinions";
    console.error('Error en fetchFeedbacks:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await Promise.all([fetchFeedbacks(), loadConfig()]);
});
</script>

<style scoped>
.feedback-section {
  padding: var(--spacing-xl) 0;
  background-color: #ffffff;
  width: 100%;
}

.section-title {
  font-size: 36px;
  color: var(--primary-color);
  margin-bottom: 60px;
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
}

.feedback-outer-container {
  max-width: 100%;
  margin: 0 auto;
  padding: 0 20px;
}

.feedback-container {
  width: 100%;
  overflow-x: auto;
  scroll-behavior: smooth;
  -webkit-overflow-scrolling: touch;
  padding: 40px 0 20px;
}

.feedback-track {
  display: grid;
  grid-auto-flow: column;
  grid-auto-columns: calc(20% - 24px);
  gap: 30px;
  padding: 0 20px;
}

.feedback-card {
  position: relative;
  background: #ffffff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  height: 280px;
  display: flex;
  flex-direction: column;
  scroll-snap-align: start;
}

.feedback-card::before {
  content: '';
  position: absolute;
  top: -25px;
  left: 50%;
  transform: translateX(-50%);
  width: 50px;
  height: 50px;
  background: var(--primary-color);
  border-radius: 50%;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>');
  background-size: 30px;
  background-position: center;
  background-repeat: no-repeat;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.feedback-content {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.feedback-text {
  font-size: 14px;
  line-height: 1.5;
  color: #333333;
  margin-bottom: 15px;
  flex-grow: 1;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  min-height: 60px;
}

.feedback-info {
  margin-top: auto;
  border-top: 1px solid #eee;
  padding-top: 10px;
}

.feedback-user {
  font-weight: bold;
  color: var(--primary-color);
  margin-bottom: 3px;
  font-size: 14px;
}

.feedback-room {
  font-size: 12px;
  color: #666666;
  margin-bottom: 3px;
}

.feedback-date {
  font-size: 12px;
  color: #666666;
}

.rating {
  display: flex;
  justify-content: center;
  gap: 3px;
  margin-top: 10px;
}

.star {
  color: #ddd;
  font-size: 16px;
  transition: color 0.3s ease;
}

.star.active {
  color: var(--primary-color);
}

.loading-container,
.error-message,
.no-feedbacks {
  text-align: center;
  padding: 40px;
  font-size: 18px;
  color: #666666;
}

.error-message {
  color: #dc3545;
}

/* Personalización de la barra de scroll */
.feedback-container::-webkit-scrollbar {
  height: 8px;
}

.feedback-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.feedback-container::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 4px;
}

.feedback-container::-webkit-scrollbar-thumb:hover {
  background: var(--primary-color-dark, #666);
}

/* Para Firefox */
.feedback-container {
  scrollbar-width: thin;
  scrollbar-color: var(--primary-color) #f1f1f1;
}

@media (max-width: 1200px) {
  .feedback-track {
    grid-auto-columns: calc(33.333% - 20px);
  }
}

@media (max-width: 768px) {
  .section-title {
    font-size: 28px;
    margin-bottom: 40px;
  }

  .feedback-track {
    grid-auto-columns: calc(100% - 40px);
  }

  .feedback-card {
    height: 250px;
    padding: 20px;
  }
}

.loading-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 20px;
  color: var(--primary-color);
  font-size: 24px;
}

.loading-indicator span {
  animation: bounce 1s infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateX(0);
  }
  50% {
    transform: translateX(10px);
  }
}
</style>