<template>
  <LoadingSpinner v-if="loading" />
  <div v-else class="hotels-container">
    <div v-for="room in rooms" :key="room.id" class="hotel-card">
      <div class="hotel-image">
        <carousel :images="getImages(room.type_room.images)" />
        <div class="heart-icon">
          <i class="fas fa-heart"></i>
        </div>
      </div>
      <div class="hotel-details" @click="goToReservation(room)">
        <!-- Nueva estructura de títulos -->
        <h2>{{ room.hotels.name }}</h2>
        <div class="room-type">
          <strong>{{ room.type_room.name }}</strong>
        </div>
        <div class="location">{{ room.hotels.city }}, {{ room.hotels.country }}</div>

        <div class="amenities">
          <div v-for="service in room.services" :key="service.id">
            <i class="fas fa-check"></i>
            {{ service.name }} ({{ service.price }}€)
          </div>
        </div>

        <div class="refund-policy">Completament reemborsable<br />Reserva ara, paga després</div>

        <div class="price">
          <div>
            <div class="original-price">{{ calculateTotalPrice(room.type_room.price + 50) }} €</div>
            <div class="discounted-price">{{ calculateTotalPrice(room.type_room.price) }} €</div>
          </div>
          <div class="rating">
            <div class="score">{{ getRandomRating() }}</div>
            <div class="reviews">
              Excel·lent
              <br />
              1003 comentaris
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Carousel from '@/components/Carousel.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const rooms = ref([])
const loading = ref(true)
const router = useRouter()
const selectedDates = ref({ startDate: '', endDate: '' })
const API_URL = import.meta.env.VITE_API_URL

const getImages = (images) => {
  try {
    return JSON.parse(images) || ['https://via.placeholder.com/150']
  } catch (error) {
    return ['https://via.placeholder.com/150']
  }
}

const getRandomRating = () => {
  return (Math.random() * (10 - 8) + 8).toFixed(1)
}

const calculateTotalPrice = (pricePerNight) => {
  if (!selectedDates.value.startDate || !selectedDates.value.endDate) {
    return pricePerNight
  }
  const startDate = new Date(selectedDates.value.startDate)
  const endDate = new Date(selectedDates.value.endDate)
  const timeDiff = Math.abs(endDate - startDate)
  const diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24))
  return pricePerNight * diffDays
}

const fetchRooms = async (filters = {}) => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filters.destination) params.append('location', filters.destination)
    if (filters.adults) params.append('people', filters.adults)
    if (filters.startDate) params.append('date_in', filters.startDate)
    if (filters.endDate) params.append('date_out', filters.endDate)

    const response = await fetch(`${API_URL}/v1/rooms/filter?${params.toString()}`, {
      method: 'GET',
      // headers: { 'Content-Type': 'application/json' },
    })

    if (!response.ok)
      throw new Error(`Error fetching rooms: ${response.status} ${response.statusText}`)

    rooms.value = await response.json()

    // Almacenar las fechas seleccionadas
    selectedDates.value.startDate = filters.startDate || ''
    selectedDates.value.endDate = filters.endDate || ''
  } catch (error) {
    console.error('Error fetching rooms:', error.message)
  } finally {
    loading.value = false
  }
}

const goToReservation = (room) => {
  const username = 'User' // Reemplaza esto con el nombre del usuario real

  // Almacenar la información en sessionStorage
  sessionStorage.setItem('selectedRoom', JSON.stringify(room))
  sessionStorage.setItem('username', username)
  sessionStorage.setItem('startDate', selectedDates.value.startDate || '')
  sessionStorage.setItem('endDate', selectedDates.value.endDate || '')

  // Redirigir a la página de reserva sin parámetros en la URL
  router.push({ name: 'reserva' })
}

defineExpose({ updateSearchParams: fetchRooms })

onMounted(() => fetchRooms())
</script>

<style scoped>
/* Importar la fuente Poppins */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

* {
  font-family: 'Poppins', sans-serif;
}

.loading {
  text-align: center;
  font-size: 24px;
  margin-top: 50px;
}

.hotels-container {
  padding: 20px 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  /* Espacio entre tarjetas */
}

.hotel-card {
  display: flex;
  background-color: var(--background-main);
  border-radius: var(--border-radius-md);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  width: 100%;
  max-width: 1000px;
  /* Tamaño máximo de la tarjeta */
  margin: 0 auto;
  /* Centrar la tarjeta */
}

.hotel-image {
  position: relative;
  width: 50%;
  /* Ajustar el ancho de la imagen */
  min-width: 250px;
  /* Ancho mínimo para la imagen */
}

.hotel-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.hotel-image .heart-icon {
  position: absolute;
  top: 10px;
  right: 10px;
  color: var(--primary-color);
  background-color: var(--background-main);
  border-radius: 50%;
  width: 30px;
  /* Ajustar el tamaño del círculo */
  height: 30px;
  /* Ajustar el tamaño del círculo */
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.hotel-details {
  padding: var(--spacing-md);
  width: 50%;
  /* Ajustar el ancho de los detalles */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.hotel-details h2 {
  margin: 0;
  font-size: var(--font-size-h3);
  font-weight: 600;
}

.hotel-details .hotel-name {
  font-size: var(--font-size-base);
  color: var(--text-primary);
  margin-top: var(--spacing-xs);
}

.hotel-details .location {
  color: var(--text-secondary);
  margin: var(--spacing-xs) 0;
}

.hotel-details .amenities {
  display: flex;
  flex-wrap: wrap;
  gap: var(--spacing-sm);
  color: var(--text-secondary);
  margin: var(--spacing-sm) 0;
}

.hotel-details .amenities i {
  margin-right: var(--spacing-xs);
}

.hotel-details .refund-policy {
  color: #4caf50;
  font-weight: 500;
  margin: var(--spacing-sm) 0;
}

.hotel-details .price {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: var(--spacing-sm) 0;
}

.hotel-details .price .original-price {
  text-decoration: line-through;
  color: var(--text-secondary);
}

.hotel-details .price .discounted-price {
  font-size: var(--font-size-h1);
  font-weight: 700;
  color: var(--text-primary);
}

.hotel-details .rating {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
}

.hotel-details .rating .score {
  background-color: #4caf50;
  color: var(--background-main);
  border-radius: var(--border-radius-sm);
  padding: var(--spacing-xs) var(--spacing-sm);
}

.hotel-details .rating .reviews {
  color: var(--text-secondary);
  font-size: var(--font-size-sm);
}

@media (max-width: 768px) {
  .hotel-card {
    flex-direction: column;
  }

  .hotel-image,
  .hotel-details {
    width: 100%;
  }
}
</style>
