<template>
  <div class="rooms-view">
    <!-- Encabezado con información de la búsqueda -->
    <div class="search-header">
      <div class="container">
        <h1 class="section-title">{{ hotelData.name }}</h1>
        <div class="search-details">
          <div class="dates">
            <i class="fas fa-calendar-alt"></i>
            {{ formattedStartDate }} - {{ formattedEndDate }}
            <span class="nights">({{ totalNights }} nits)</span>
          </div>
          <div class="guests-info">
            <i class="fas fa-users"></i>
            {{ searchParams.adults }} adults · {{ searchParams.rooms }} habitacions
          </div>
        </div>
      </div>
    </div>

    <!-- Listado de habitaciones -->
    <main class="main-content container">
      <div v-if="loading" class="loading-container">
        <p>Carregant habitacions...</p>
      </div>

      <div v-else-if="error" class="error-message">
        <p>Error: {{ error }}</p>
      </div>

      <div v-else-if="filteredRooms.length === 0" class="no-rooms">
        <p>No hi ha habitacions disponibles per als filtres seleccionats.</p>
      </div>

      <div v-else class="rooms-grid">
        <div 
          v-for="(room, index) in filteredRooms" 
          :key="index" 
          class="room-card"
        >
          <div class="room-image">
            <img :src="room.image" :alt="room.name">
          </div>
          <div class="room-details">
            <h2 class="room-type">{{ room.name }}</h2>
            <div class="room-info">
              <div class="room-capacity">
                <i class="fas fa-user-friends"></i>
                Capacitat: {{ room.capacity }} persones
              </div>
              <div class="amenities">
                <div v-for="service in room.services" :key="service.name" class="amenity">
                  <i class="fas fa-check"></i>
                  {{ service.name }} ({{ service.price }}€)
                </div>
              </div>
              <div class="price-container">
                <div class="price-per-night">
                  {{ room.price }}€/nit
                </div>
                <div class="total-price">
                  Total: {{ calculateTotalPrice(room.price) }}€
                </div>
                <button class="reserve-button" @click="openReservationForm(room)">
                  Reservar ara
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal de reserva -->
    <div v-if="showReservationModal" class="modal-overlay" @click="closeReservationModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>Reservar {{ selectedRoom.name }}</h2>
          <button class="close-button" @click="closeReservationModal">&times;</button>
        </div>
        
        <form @submit.prevent="submitReservation" class="reservation-form">
          <!-- Estado de la reserva -->
          <div v-if="reservationStatus" :class="['status-message', reservationStatus]">
            <i :class="['fas', reservationStatus === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle']"></i>
            <span v-if="reservationStatus === 'success'">Reserva realitzada amb èxit!</span>
            <span v-else>{{ error }}</span>
          </div>

          <div class="form-group">
            <label for="email">Correu electrònic</label>
            <input 
              type="email" 
              id="email" 
              v-model="reservationEmail" 
              required 
              placeholder="el-teu@email.com"
              :disabled="isSubmitting"
            >
          </div>
          
          <div class="reservation-summary">
            <h3>Resum de la reserva</h3>
            <div class="summary-item">
              <span class="label">Dates:</span>
              <span>{{ formattedStartDate }} - {{ formattedEndDate }}</span>
            </div>
            <div class="summary-item">
              <span class="label">Hostes:</span>
              <span>{{ searchParams.adults }} adults</span>
            </div>
            <div class="summary-item">
              <span class="label">Habitació:</span>
              <span>{{ selectedRoom.name }}</span>
            </div>
            <div class="summary-item total">
              <span class="label">Total:</span>
              <span class="price">{{ calculateTotalPrice(selectedRoom.price) }}€</span>
            </div>
          </div>

          <div class="form-actions">
            <button 
              type="button" 
              class="cancel-button" 
              @click="closeReservationModal"
              :disabled="isSubmitting"
            >
              Cancel·lar
            </button>
            <button 
              type="submit" 
              class="confirm-button" 
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting">
                <i class="fas fa-spinner fa-spin"></i> Processant...
              </span>
              <span v-else>Confirmar reserva</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const API_URL = import.meta.env.VITE_API_URL;
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER;

// Datos reactivos
const hotelData = ref({});
const availableRoomTypes = ref([]);
const loading = ref(true);
const error = ref(null);
const showReservationModal = ref(false);
const selectedRoom = ref(null);
const reservationEmail = ref('');
const isSubmitting = ref(false);
const reservationStatus = ref(null);

// Función para parsear imágenes (similar a PrincipalRoomsComponent)
const getParsedImages = (imagesString) => {
  if (!imagesString) return ['https://via.placeholder.com/400x300'];
  try {
    const parsed = JSON.parse(imagesString);
    return Array.isArray(parsed) && parsed.length > 0 ? parsed : ['https://via.placeholder.com/400x300'];
  } catch (error) {
    console.warn('Error parsing images string:', imagesString, error);
    return ['https://via.placeholder.com/400x300'];
  }
};

// Mapeo de capacidades y precios (actualizar según datos reales de la API)
const roomSpecs = {
  'Estándar': { capacity: 2, price: 100, image: 'https://via.placeholder.com/400x300' },
  'Suite': { capacity: 4, price: 200, image: 'https://via.placeholder.com/400x300' },
  'Familiar': { capacity: 6, price: 150, image: 'https://via.placeholder.com/400x300' }
};

// Parámetros de búsqueda
const searchParams = computed(() => ({
  date_in: route.query.date_in,
  date_out: route.query.date_out,
  adults: parseInt(route.query.adults) || 2,
  rooms: parseInt(route.query.rooms) || 1
}));

// Habitaciones filtradas
const filteredRooms = computed(() => {
  return availableRoomTypes.value.filter(room => 
    room.capacity >= searchParams.value.adults
  );
});

// Cálculos de fechas y precios
const totalNights = computed(() => {
  const start = new Date(searchParams.value.date_in);
  const end = new Date(searchParams.value.date_out);
  return Math.ceil((end - start) / (1000 * 60 * 60 * 24));
});

const formattedStartDate = computed(() => 
  new Date(searchParams.value.date_in).toLocaleDateString('es-ES', { day: 'numeric', month: 'long' })
);

const formattedEndDate = computed(() => 
  new Date(searchParams.value.date_out).toLocaleDateString('es-ES', { day: 'numeric', month: 'long' })
);

const calculateTotalPrice = (pricePerNight) => {
  const total = (parseFloat(pricePerNight) * totalNights.value * searchParams.value.rooms);
  return total.toFixed(2);
};

// Helpers
const getRoomCapacity = (room) => room.capacity;
const getRoomPrice = (room) => room.price;
const getRoomImage = (room) => room.image;

// Funciones de reserva
const openReservationForm = (room) => {
  selectedRoom.value = room;
  showReservationModal.value = true;
};

const closeReservationModal = () => {
  showReservationModal.value = false;
  selectedRoom.value = null;
  reservationEmail.value = '';
};

const submitReservation = async () => {
  try {
    isSubmitting.value = true;
    reservationStatus.value = null;

    // Verificar que tenemos el código del hotel
    if (!API_IDENTIFIER) {
      console.error('Código del hotel no disponible');
      throw new Error('No se ha podido obtener el código del hotel');
    }

    // Formatear las fechas en el formato esperado por el backend (YYYY-MM-DD)
    const formattedDateIn = new Date(searchParams.value.date_in).toISOString().split('T')[0];
    const formattedDateOut = new Date(searchParams.value.date_out).toISOString().split('T')[0];

    const reservationData = {
      email: reservationEmail.value,
      date_in: formattedDateIn,
      date_out: formattedDateOut,
      people: parseInt(searchParams.value.adults),
      type_room_id: selectedRoom.value.id,
      hotel_code: API_IDENTIFIER, // Usar el código del hotel directamente
      rooms: parseInt(searchParams.value.rooms),
      total_price: parseFloat(calculateTotalPrice(selectedRoom.value.price))
    };

    console.log('Datos de la reserva a enviar:', reservationData);

    const response = await fetch(`${API_URL}/v2/create-reserve`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(reservationData)
    });

    const data = await response.json();
    console.log('Respuesta completa del servidor:', data);

    if (!response.ok) {
      console.error('Error respuesta:', data);
      if (data.errors) {
        const errorMessages = Object.values(data.errors).flat().join('\n');
        throw new Error(errorMessages);
      }
      throw new Error(data.message || data.error || 'Error en processar la reserva');
    }

    // Reserva exitosa
    reservationStatus.value = 'success';
    setTimeout(() => {
      closeReservationModal();
      fetchHotelData();
    }, 2000);

  } catch (err) {
    console.error('Error en la reserva:', err);
    reservationStatus.value = 'error';
    error.value = typeof err === 'string' ? err : 
                  err.message || 'Error en processar la reserva. Si us plau, torna-ho a provar més tard.';
  } finally {
    isSubmitting.value = false;
  }
};

// Fetch data
const fetchHotelData = async () => {
  try {
    // Obtener hotel
    const hotelResponse = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}`);
    if (!hotelResponse.ok) {
      const errorText = await hotelResponse.text();
      console.error('Error en resposta del hotel:', errorText);
      throw new Error(`Error HTTP: ${hotelResponse.status}`);
    }
    const hotelDataResponse = await hotelResponse.json();
    console.log('Datos del hotel cargados:', hotelDataResponse);

    // Asignar los datos del hotel
    if (hotelDataResponse && hotelDataResponse.hotel) {
      hotelData.value = {
        ...hotelDataResponse.hotel,
        code: API_IDENTIFIER // Mantener el código original del hotel
      };
      console.log('Datos del hotel asignados:', hotelData.value);
    } else {
      throw new Error('Formato de respuesta del hotel inválido');
    }

    // Obtener habitaciones disponibles con los filtros
    const params = new URLSearchParams({
      date_in: searchParams.value.date_in,
      date_out: searchParams.value.date_out,
      people: searchParams.value.adults
    });

    const roomsResponse = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}/search?${params}`);
    if (!roomsResponse.ok) {
      const errorText = await roomsResponse.text();
      console.error('Error en la resposta de habitacions:', errorText);
      throw new Error(`Error al obtener habitaciones: ${roomsResponse.status}`);
    }

    const data = await roomsResponse.json();
    console.log('Dades de habitacions rebudes:', data);

    if (data && data.room_types) {
      availableRoomTypes.value = data.room_types.map(room => {
        const parsedImages = getParsedImages(room.images); // Parsear el string JSON de imágenes
        return {
          id: room.id,
          name: room.name,
          price: room.price,
          capacity: room.capacity || 2,
          image: parsedImages[0], // Usar la primera imagen del array
          allImages: parsedImages, // Guardar todas por si se necesitan luego
          services: room.services || []
        };
      });
    } else {
      availableRoomTypes.value = [];
    }

  } catch (err) {
    console.error('Error detallat:', err);
    error.value = "Error carregant dades: " + err.message;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchHotelData();
});
</script>

<style scoped>
.rooms-view {
  min-height: 100vh;
  background-color: #ffffff;
}

.search-header {
  background: var(--primary-color);
  color: white;
  padding: 2rem 0;
  margin-bottom: 2rem;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.section-title {
  font-size: 36px;
  margin-bottom: 1rem;
  color: white;
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
}

.search-details {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  font-size: 1.1rem;
}

.rooms-grid {
  display: grid;
  gap: 2rem;
  padding: 2rem 0;
}

.room-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  display: flex;
  transition: transform 0.3s ease;
}

.room-card:hover {
  transform: translateY(-5px);
}

.room-image {
  width: 40%;
  min-width: 300px;
}

.room-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.room-details {
  padding: 2rem;
  flex: 1;
}

.room-type {
  font-size: 1.8rem;
  color: var(--primary-color);
  margin-bottom: 1rem;
}

.room-info {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.room-capacity {
  font-size: 1.1rem;
  color: #666;
}

.amenities {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
}

.amenity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: #666;
}

.price-container {
  margin-top: auto;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.price-per-night {
  font-size: 1.1rem;
  color: #666;
}

.total-price {
  font-size: 1.8rem;
  color: var(--primary-color);
  font-weight: bold;
  margin: 0.5rem 0;
}

.reserve-button {
  background-color: var(--primary-color);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
  width: 100%;
  margin-top: 1rem;
}

.reserve-button:hover {
  background-color: var(--secondary-color);
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 0;
  border-radius: 15px;
  width: 90%;
  max-width: 500px;
  overflow: hidden;
}

.modal-header {
  background: var(--primary-color);
  color: white;
  padding: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.close-button {
  background: none;
  border: none;
  color: white;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
}

.reservation-form {
  padding: 2rem;
}

.reservation-summary {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin: 1.5rem 0;
}

.reservation-summary h3 {
  margin: 0 0 1rem 0;
  color: var(--primary-color);
  font-size: 1.2rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.8rem;
  font-size: 1rem;
}

.summary-item .label {
  color: #666;
  font-weight: 500;
}

.summary-item.total {
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid #ddd;
  font-weight: bold;
}

.summary-item.total .price {
  color: var(--primary-color);
  font-size: 1.2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  color: #333;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
}

.form-group input:focus {
  border-color: var(--primary-color);
  outline: none;
}

.form-actions {
  display: flex;
  gap: 1rem;
  margin-top: 2rem;
}

.cancel-button,
.confirm-button {
  padding: 0.8rem 1.5rem;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  border: none;
  flex: 1;
}

.cancel-button {
  background: #f1f1f1;
  color: #333;
}

.confirm-button {
  background: var(--primary-color);
  color: white;
}

.confirm-button:hover {
  background: var(--secondary-color);
}

/* Estilos para los estados de la reserva */
.status-message {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.status-message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.status-message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.status-message i {
  font-size: 1.2rem;
}

/* Estilos para el botón deshabilitado */
.confirm-button:disabled,
.cancel-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Estilos para el spinner */
.fa-spinner {
  margin-right: 0.5rem;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.fa-spin {
  animation: spin 1s linear infinite;
}

@media (max-width: 768px) {
  .search-details {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .room-card {
    flex-direction: column;
  }

  .room-image {
    width: 100%;
    min-width: auto;
    height: 200px;
  }

  .room-details {
    padding: 1.5rem;
  }

  .room-type {
    font-size: 1.5rem;
  }

  .total-price {
    font-size: 1.5rem;
  }

  .modal-content {
    width: 95%;
    margin: 1rem;
  }

  .reservation-form {
    padding: 1.5rem;
  }
}
</style>