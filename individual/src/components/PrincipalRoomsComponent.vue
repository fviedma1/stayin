<template>
  <section v-if="visible" class="habitacio-view" :style="{ '--section-order': sectionOrder }">
    <h2 class="section-title">Les Nostres Habitacions</h2>
    
    <div v-if="loading" class="estado-carga">
      <p>Carregant habitacions...</p>
    </div>
    <div v-else-if="error" class="estado-error">
      <p>Error: {{ error }}</p>
    </div>
    
    <template v-else>
      <div class="selector-container">
        <div class="selector-inner container">
          <button
            v-for="(roomType, index) in roomTypes"
            :key="roomType.name"
            @click="selectRoomType(index)"
            :class="['type-button', { 'active': selectedIndex === index }]"
          >
            {{ roomType.name.toUpperCase() }}
          </button>
        </div>
      </div>

      <main class="main-content container mt-xl">
        <div class="content-wrapper">
          <div class="carousel-container">
            <div class="carousel-track" @mouseenter="pauseSlider" @mouseleave="resumeSlider">
              <img 
                v-for="(image, index) in currentRoom.images"
                :key="index"
                :src="image"
                class="carousel-image border-lg"
                :alt="`Imatge ${index + 1}`"
                :class="{ 'active': currentSlide === index }"
              />
            </div>
            <div class="carousel-controls">
              <button class="control-button prev" @click="prevSlide">‹</button>
              <button class="control-button next" @click="nextSlide">›</button>
            </div>
          </div>

          <div class="room-details border-md">
            <h2 class="room-title text-primary">{{ currentRoom.name.toUpperCase() }}</h2>
            <p class="room-description text-secondary">{{ currentRoom.description }}</p>
            <div class="price-container">
              <span class="price bg-primary text-light">{{ formattedPrice }}</span>
            </div>
          </div>
        </div>
      </main>
    </template>

    <div v-if="!loading && !error && roomTypes.length === 0" class="sin-datos">
      <p>No hi ha habitacions disponibles</p>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const API_URL = import.meta.env.VITE_API_URL
const IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER

// Variables originales
const roomTypes = ref([])
const selectedIndex = ref(0)
const currentSlide = ref(0)
const sliderInterval = ref(null)
const loading = ref(true)
const error = ref(null)
const visible = ref(true)
const sectionOrder = ref(0)

// Funciones originales
const getImages = (images) => {
  try {
    return JSON.parse(images) || ['https://via.placeholder.com/150']
  } catch (error) {
    return ['https://via.placeholder.com/150']
  }
}

const roomDescriptions = {
  individual: "Habitació acollidora i funcional perfecta per a estades curtes. Inclou llit individual, bany privat i amenities bàsics.",
  doble: "Habitació espaiosa amb llit doble, ideal per a parelles. Inclou bany privat, escriptori i vistes a l'exterior.",
  triple: "Habitació àmplia amb tres llits individuals o un llit doble i un individual. Perfecta per a grups petits o famílies.",
  familiar: "Espai ideal per a famílies amb zona d'estar separada. Inclou un llit doble i dos individuals, bany complet i zona de jocs.",
  suite: "Experiència de luxe amb saló independent, terrassa privada, bany amb jacuzzi i serveis premium les 24 hores."
}

// Computed originales
const currentRoom = computed(() => ({
  name: roomTypes.value[selectedIndex.value]?.name || '',
  description: roomDescriptions[roomTypes.value[selectedIndex.value]?.name.toLowerCase()] || 'No hi ha informació disponible',
  price: roomTypes.value[selectedIndex.value]?.price || 0,
  images: getImages(roomTypes.value[selectedIndex.value]?.images)
}))

const formattedPrice = computed(() => {
  return currentRoom.value.price 
    ? new Intl.NumberFormat('ca-ES', {
        style: 'currency',
        currency: 'EUR'
      }).format(currentRoom.value.price)
    : 'Llegir més'
})

// Métodos originales
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % currentRoom.value.images.length
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + currentRoom.value.images.length) % currentRoom.value.images.length
}

const startSlider = () => {
  sliderInterval.value = setInterval(nextSlide, 5000)
}

const pauseSlider = () => {
  clearInterval(sliderInterval.value)
}

const resumeSlider = () => {
  startSlider()
}

const selectRoomType = (index) => {
  selectedIndex.value = index
  currentSlide.value = 0
  pauseSlider()
  resumeSlider()
}

// Llamada API original
const fetchRoomTypes = async () => {
  try {
    console.log('Intentando obtener habitaciones de:', `${API_URL}/v2/hotels/${IDENTIFIER}/typerooms`)
    const response = await fetch(`${API_URL}/v2/hotels/${IDENTIFIER}/typerooms`)
    
    // Log de la respuesta completa
    console.log('Respuesta del servidor:', response)
    
    if (!response.ok) {
      const errorText = await response.text()
      console.error('Error response:', errorText)
      throw new Error(`Error ${response.status}: ${errorText}`)
    }
    
    const data = await response.json()
    console.log('Datos recibidos:', data)
    
    if (!data.room_types?.length) {
      console.warn('No se encontraron habitaciones en la respuesta')
      throw new Error('No se encontraron habitaciones')
    }
    
    // Definir el orden deseado de las habitaciones
    const roomOrder = {
      'individual': 1,
      'doble': 2,
      'triple': 3,
      'familiar': 4,
      'suite': 5
    }
    
    // Ordenar las habitaciones según el orden definido
    roomTypes.value = data.room_types
      .map(room => ({
        name: room.name || 'Habitació sense nom',
        price: room.price || 0,
        images: room.images
      }))
      .sort((a, b) => {
        const orderA = roomOrder[a.name.toLowerCase()] || 999
        const orderB = roomOrder[b.name.toLowerCase()] || 999
        return orderA - orderB
      })
    
    console.log('Habitaciones procesadas y ordenadas:', roomTypes.value)
    
    if (roomTypes.value.length > 0) {
      selectedIndex.value = 0
      console.log('Primera habitación seleccionada:', roomTypes.value[0])
    }
    
  } catch (err) {
    error.value = err.message
    console.error('Error detallado en fetchRoomTypes:', err)
  } finally {
    loading.value = false
  }
}

// Función para cargar la configuración
const loadConfig = async () => {
  try {
    const response = await fetch(`${API_URL}/v2/hotels/${IDENTIFIER}/sections`)
    if (!response.ok) {
      console.error('Error en respuesta de config:', await response.text())
      return
    }
    
    const sections = await response.json()
    const roomSection = sections.find(s => s.slug === 'room_types')
    
    if (roomSection?.pivot) {
      visible.value = Boolean(roomSection.pivot.is_visible)
      sectionOrder.value = parseInt(roomSection.pivot.order) || 0
    }
  } catch (error) {
    console.error('Error cargando configuración:', error)
  }
}

onMounted(async () => {
  try {
    console.log('Componente montado, iniciando carga de datos')
    // 1. Cargar habitaciones
    await fetchRoomTypes()
    startSlider()
    // 2. Cargar configuración de forma independiente
    loadConfig()
  } catch (error) {
    console.error('Error en la carga inicial:', error)
  }
})

onBeforeUnmount(() => {
  pauseSlider()
})
</script>

<style scoped>
.habitacio-view {
  padding: var(--spacing-l, 30px) 0; /* Móvil: Reducido */
  background-color: #ffffff;
  width: 100%;
  box-sizing: border-box;
}

.section-title {
  font-size: 28px; /* Móvil: Reducido */
  color: var(--primary-color, #333);
  text-align: center;
  margin-bottom: 30px; /* Móvil: Reducido */
  font-weight: bold;
  text-transform: uppercase;
}

.estado-carga,
.estado-error,
.sin-datos {
  text-align: center;
  padding: var(--spacing-m, 20px);
  font-size: 16px; /* Móvil */
  color: var(--text-secondary, #555);
  margin: 20px auto;
  max-width: 90%;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.estado-error {
  color: var(--status-error, #dc3545);
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
}

.selector-container {
  background-color: #ffffff;
  margin-bottom: 25px; /* Móvil: Reducido */
  position: relative;
  overflow-x: auto; /* Permitir scroll horizontal en el contenedor principal del selector */
  -webkit-overflow-scrolling: touch; /* Scroll suave en iOS */
}

.selector-inner {
  display: flex; /* Cambiado de grid a flex para scroll horizontal más natural */
  justify-content: flex-start; /* Alinear al inicio para scroll */
  gap: 15px; /* Móvil: Reducido */
  padding: 10px 15px; /* Padding para que no pegue a los bordes */
  white-space: nowrap; /* Evitar que los botones se rompan en varias líneas */
  /* width: max-content; Quitar si el contenedor ya gestiona el scroll */
}

.type-button {
  padding: 8px 15px; /* Móvil: Reducido */
  border: none;
  background: none;
  color: #000000;
  font-size: 14px; /* Móvil: Reducido */
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  font-weight: 500;
  flex-shrink: 0; /* Evitar que los botones se encojan */
}

.type-button.active {
  color: var(--primary-color, #007bff);
}

.type-button::after {
  content: '';
  position: absolute;
  bottom: -5px; /* Ajustado */
  left: 0;
  width: 100%;
  height: 2px;
  background: transparent;
  transition: background-color 0.3s ease;
}

.type-button.active::after {
  background: var(--primary-color, #007bff);
}

/* Estructura de contenido principal */
.main-content {
  /* El padding container ya existe en la clase .container, si se usa */
  /* padding: 0 var(--spacing-m, 15px); Móvil */
}

.content-wrapper {
  display: grid;
  grid-template-columns: 1fr; /* Móvil: Una columna por defecto */
  gap: 25px; /* Móvil: Reducido */
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 15px; /* Padding para el content-wrapper */
}

.carousel-container {
  position: relative;
  width: 100%;
  height: 300px; /* Móvil: Altura del carrusel ajustada */
  overflow: hidden;
  border-radius: 8px;
  background-color: #f0f0f0; /* Placeholder si las imágenes tardan en cargar */
}

.carousel-track {
  position: relative;
  height: 100%;
  display: flex; /* Usar flex para el track si las imágenes van a estar una al lado de la otra */
                 /* Si es fade, la posición absoluta en .carousel-image es correcta */
}

.carousel-image {
  position: absolute; /* Para efecto fade */
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 0.5s ease-in-out; /* Transición más suave */
  border-radius: inherit; /* Heredar el borde del contenedor */
}

.carousel-image.active {
  opacity: 1;
}

.carousel-controls {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 100%;
  display: flex;
  justify-content: space-between;
  padding: 0 10px; /* Móvil: Padding reducido */
  pointer-events: none; /* Para que no interfieran con el swipe si se implementa */
}

.control-button {
  width: 35px; /* Móvil */
  height: 35px; /* Móvil */
  background: rgba(0, 0, 0, 0.5);
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 18px; /* Móvil */
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.3s ease;
  pointer-events: all; /* Permitir click en los botones */
}
.control-button:hover {
  background: rgba(0,0,0,0.7);
}

.room-details {
  background-color: #ffffff;
  padding: 20px; /* Móvil: Reducido */
  border-radius: 8px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); /* Sombra más suave */
}

.room-title {
  font-size: 22px; /* Móvil: Reducido */
  color: var(--primary-color, #007bff);
  margin-bottom: 10px; /* Móvil */
  font-weight: bold;
}

.room-description {
  font-size: 14px; /* Móvil */
  line-height: 1.6;
  color: #333333;
  margin-bottom: 20px; /* Móvil */
}

.price-container {
  margin-top: 20px; /* Móvil */
  text-align: left; /* Alinear precio a la izquierda en móvil */
}

.price {
  display: inline-block;
  padding: 10px 18px; /* Móvil: Reducido */
  font-size: 20px; /* Móvil: Reducido */
  font-weight: bold;
  color: #ffffff;
  background-color: var(--primary-color, #007bff);
  border-radius: 4px;
}

/* Tablet Styles */
@media (min-width: 768px) {
  .habitacio-view {
    padding: var(--spacing-xl, 40px) 0;
  }
  .section-title {
    font-size: 32px;
    margin-bottom: 40px;
  }
  .selector-container {
    overflow-x: visible; /* Ya no necesita scroll */
  }
  .selector-inner {
    justify-content: center; /* Centrar botones en tablet */
    gap: 25px;
    padding: 10px 20px;
  }
  .type-button {
    padding: 10px 20px;
    font-size: 15px;
  }
  .content-wrapper {
    padding: 0 20px;
    /* grid-template-columns: 1fr; Se mantiene 1 columna hasta más grande */
    gap: 30px;
  }
  .carousel-container {
    height: 400px;
  }
   .control-button {
    width: 40px;
    height: 40px;
    font-size: 20px;
  }
  .room-details {
    padding: 30px;
  }
  .room-title {
    font-size: 26px;
  }
  .room-description {
    font-size: 15px;
  }
  .price {
    font-size: 22px;
  }
  .price-container {
    text-align: right; /* Alinear precio a la derecha en tablet+ */
  }
  .estado-carga, .estado-error, .sin-datos {
    font-size: 18px;
    max-width: 600px;
  }
}

/* Desktop Styles */
@media (min-width: 1024px) {
  .section-title {
    font-size: 36px;
    margin-bottom: 50px;
  }
  .selector-inner {
    gap: 30px; /* Espacio original o ajustado */
  }
  .type-button {
    font-size: 16px; /* Tamaño original */
  }
  .content-wrapper {
    grid-template-columns: 1.5fr 1fr; /* Layout de dos columnas */
    gap: 40px; /* Espacio original */
    padding: 0 40px;
  }
  .carousel-container {
    height: 450px; /* Ajustar altura para desktop */
  }
  .room-details {
    padding: 35px;
  }
  .room-title {
    font-size: 28px; /* Ajustar si es necesario */
  }
}

@media (min-width: 1200px) {
    .carousel-container {
        height: 500px; /* Altura original para desktop grandes */
    }
    .room-title {
        font-size: 32px; /* Tamaño original */
    }
     .room-details {
        padding: 40px; /* Padding original */
    }
}
</style>