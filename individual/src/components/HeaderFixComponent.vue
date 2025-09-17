<!-- filepath: /c:/Users/viedm/OneDrive/Documentos/GIT/grup-07-fernando-viedma/individual/src/components/HeaderFixComponent.vue -->
<template>
  <header class="header">
    <nav class="nav">
      <!-- Sección izquierda con enlaces -->
      <div class="nav-section left">
        <ul class="nav-items">
          <li>
            <router-link to="/" class="nav-link">Inici</router-link>
          </li>
          <li>
            <router-link to="/noticies" class="nav-link">Notícies</router-link>
          </li>
        </ul>
      </div>

      <!-- Nombre del Hotel Centrado -->
      <div class="nav-section center">
        <div v-if="!hotelLoading && !hotelError" class="hotel-name">
          {{ hotelName }}
        </div>
        <div v-else-if="hotelLoading" class="loading-text">Loading hotel...</div>
      </div>
    </nav>
  </header>
</template>

<script>
import { useRouter } from 'vue-router';

const API_URL = import.meta.env.VITE_API_URL;
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER;

export default {
  name: 'HeaderFixComponent',
  setup() {
    const router = useRouter();
    return { router };
  },
  data() {
    return {
      hotelName: '',
      hotelLoading: true,
      hotelError: null
    }
  },
  methods: {
    async fetchHotelData() {
      try {
        const response = await fetch(`${API_URL}/v2/hotels/${API_IDENTIFIER}`);
        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }
        
        const data = await response.json();
        
        if (!data.hotel) {
          throw new Error('Invalid response format: missing hotel data');
        }
        
        this.hotelName = data.hotel.name;
        this.hotelError = null;
      } catch (err) {
        this.hotelError = err.message;
        console.error('Error fetching hotel:', err);
      } finally {
        this.hotelLoading = false;
      }
    }
  },
  mounted() {
    this.fetchHotelData();
  }
}
</script>

<style scoped>
:root {
  --background-main: #ffffff;
  --primary-color: #cc7a39;
  --secondary-color: #FFA462;
  --text-primary: #212529;
  --text-secondary: #495057;
  --border-color: #dee2e6;
  --hover-color: #e9ecef;
}

.header {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  width: 100%;
  background-color: var(--background-main);
  box-shadow: 0 2px 8px rgba(204, 122, 57, 0.15);
  z-index: 1000;
  padding: 0.5rem 1rem;
}

.nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  max-width: 100%;
  margin: 0 auto;
}

.nav-section {
  display: flex;
  align-items: center;
}

.nav-section.left {
  display: none;
}

.nav-section.center {
  flex: 1;
  text-align: center;
  padding: 0 1rem;
}

.hotel-name {
  font-size: clamp(1.1rem, 4vw, 1.5rem);
  font-weight: 600;
  color: var(--primary-color);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

.loading-text {
  color: var(--text-secondary);
  font-size: clamp(0.8rem, 3vw, 0.875rem);
}

.nav-items {
  display: flex;
  gap: 1rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-link {
  text-decoration: none;
  color: var(--text-secondary);
  font-weight: 500;
  transition: color 0.3s ease;
  font-size: clamp(0.8rem, 3vw, 1rem);
  position: relative;
  white-space: nowrap;
}

.nav-link::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: -4px;
  left: 0;
  background-color: var(--primary-color);
  transition: width 0.3s ease;
}

.nav-link:hover {
  color: var(--primary-color);
}

.nav-link:hover::after {
  width: 100%;
}

/* Small devices (576px and up) */
@media (min-width: 36em) {
  .hotel-name {
    letter-spacing: 1px;
  }
}

/* Tablet (768px and up) */
@media (min-width: 48em) {
  .header {
    padding: 1rem;
  }

  .nav {
    max-width: 720px;
  }

  .nav-section.left {
    display: flex;
    flex: 1;
  }

  .nav-items {
    gap: 1.5rem;
  }

  .nav-section.center {
    flex: 0 0 auto;
    padding: 0;
  }
}

/* Desktop (992px and up) */
@media (min-width: 62em) {
  .nav {
    max-width: 960px;
  }

  .nav-items {
    gap: 2rem;
  }

  .hotel-name {
    font-size: clamp(1.5rem, 2vw, 1.75rem);
  }
}

/* Large Desktop (1200px and up) */
@media (min-width: 75em) {
  .nav {
    max-width: 1140px;
  }

  .nav-link {
    font-size: 1rem;
  }
}

/* Extra Large screens (1400px and up) */
@media (min-width: 87.5em) {
  .nav {
    max-width: 1320px;
  }
}
</style>