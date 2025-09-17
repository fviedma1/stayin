<template>
  <div class="hotel-component-container">
    <h1>Informació de l'hotel</h1>
    <div v-if="loading" class="loading-message">
      <p>Carregant...</p>
    </div>
    <div v-else-if="error" class="error-message">
      <p class="error">Error: {{ error }}</p>
    </div>
    <div v-else-if="hotel" class="hotel-details">
      <p><strong>Nom:</strong> {{ hotel.name }}</p>
      <p><strong>Adreça:</strong> {{ hotel.address }}</p>
      <p><strong>Ciutat:</strong> {{ hotel.city }}</p>
      <p><strong>País:</strong> {{ hotel.country }}</p>
      <p><strong>Telèfon:</strong> {{ hotel.telephone }}</p>
      <p><strong>Correu electrònic:</strong> {{ hotel.email }}</p>
    </div>
    <div v-else class="no-data-message">
      <p>No hi ha dades de l'hotel disponibles.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const hotel = ref(null)
const loading = ref(true)
const error = ref(null)
const API_URL = import.meta.env.VITE_API_URL
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER

const fetchHotel = async () => {
  try {
    console.log('Fetching hotel data from:', `${API_URL}/v2/hotels/${API_IDENTIFIER}`);
    const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}`)
    
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
    }
    
    const data = await response.json()
    console.log('Received hotel data:', data);
    
    if (!data.hotel) {
      throw new Error('Invalid response format: missing hotel data')
    }
    
    hotel.value = data.hotel
    error.value = null
  } catch (err) {
    error.value = err.message
    console.error('Error fetching hotel data:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchHotel())
</script>

<style scoped>
.hotel-component-container {
  font-family: Arial, sans-serif;
  padding: 1rem;
  max-width: 1200px;
  margin: 0 auto;
  box-sizing: border-box;
}

h1 {
  text-align: center;
  color: #333;
  margin-bottom: 1.5rem;
  font-size: 2rem;
}

.loading-message,
.error-message,
.no-data-message {
  text-align: center;
  padding: 1rem;
  margin-top: 1rem;
  border-radius: 4px;
}

.loading-message {
  color: #555;
}

.error-message .error {
  color: #D8000C;
  background-color: #FFD2D2;
  padding: 0.75rem;
  border-radius: 4px;
  border: 1px solid #D8000C;
  font-weight: bold;
}

.no-data-message {
  color: #777;
}

.hotel-details {
  background-color: #f9f9f9;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.hotel-details p {
  margin-bottom: 0.75rem;
  line-height: 1.6;
  font-size: 1rem;
  color: #444;
}

.hotel-details p strong {
  color: #222;
  min-width: 120px;
  display: inline-block;
}

@media (min-width: 600px) {
  .hotel-component-container {
    padding: 1.5rem;
  }

  h1 {
    font-size: 2.25rem;
  }

  .hotel-details {
    padding: 1.5rem;
  }

  .hotel-details p {
    font-size: 1.1rem;
  }
}

@media (min-width: 992px) {
  .hotel-component-container {
    padding: 2rem;
  }
  
  h1 {
    font-size: 2.5rem;
    text-align: left;
  }

  .hotel-details {
    padding: 2rem;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0.5rem 1rem;
    align-items: center;
  }

  .hotel-details p {
    margin-bottom: 0.5rem;
    font-size: 1.15rem;
  }

  .hotel-details p strong {
     margin-right: 0.5rem;
  }
}
</style>