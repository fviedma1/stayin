<!-- filepath: /c:/Users/viedm/OneDrive/Documentos/GIT/grup-07-fernando-viedma/individual/src/components/SearchBarComponent.vue -->
<template>
  <div class="search-container">
    <form @submit.prevent="searchRooms" class="search-form">
      <!-- Selector de fechas -->
      <div class="form-group date-group">
        <button type="button" class="input-field date-toggle" @click="toggleDatePicker">
          {{ formattedDates }}
        </button>
        <div v-if="showDatePicker" class="date-picker">
          <label>Data d'entrada:</label>
          <input type="date" v-model="startDate" class="date-input" />
          <label>Data de sortida:</label>
          <input type="date" v-model="endDate" class="date-input" />
          <button type="button" class="apply-button" @click="applyDates">Aplicar</button>
        </div>
      </div>

      <!-- Selector de huéspedes -->
      <div class="form-group dropdown-group">
        <button type="button" @click="toggleDropdown" class="input-field dropdown-toggle">
          {{ guests }}
        </button>
        <div v-if="dropdownOpen" class="dropdown-menu">
          <div class="dropdown-row">
            <div class="dropdown-label">Adults</div>
            <div class="dropdown-controls">
              <button type="button" @click="decrement('adults')" class="control-button">-</button>
              <span class="dropdown-value">{{ adults }}</span>
              <button type="button" @click="increment('adults')" class="control-button">+</button>
            </div>
          </div>
          <div class="dropdown-row">
            <div class="dropdown-label">Habitacions</div>
            <div class="dropdown-controls">
              <button type="button" @click="decrement('rooms')" class="control-button">-</button>
              <span class="dropdown-value">{{ rooms }}</span>
              <button type="button" @click="increment('rooms')" class="control-button">+</button>
            </div>
          </div>
          <button type="button" class="apply-button" @click="applyGuests">Aplicar</button>
        </div>
      </div>

      <!-- Botón de búsqueda -->
      <div class="form-group search-button-container">
        <button type="submit" class="search-button">Cercar</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const API_URL = import.meta.env.VITE_API_URL;
const API_IDENTIFIER = import.meta.env.VITE_API_IDENTIFIER;

// Datos reactivos
const startDate = ref('');
const endDate = ref('');
const adults = ref(2);
const rooms = ref(1);
const guests = ref('2 adults · 1 habitació');
const dropdownOpen = ref(false);
const showDatePicker = ref(false);

// Lógica de fechas
const toggleDatePicker = () => showDatePicker.value = !showDatePicker.value;
const applyDates = () => showDatePicker.value = false;
const formattedDates = computed(() => {
  if (startDate.value && endDate.value) {
    const options = { day: '2-digit', month: 'short' };
    return `${new Date(startDate.value).toLocaleDateString('es-ES', options)} - ${new Date(endDate.value).toLocaleDateString('es-ES', options)}`;
  }
  return 'Duració estada';
});

// Lógica de huéspedes
const toggleDropdown = () => dropdownOpen.value = !dropdownOpen.value;
const increment = (type) => type === 'adults' ? adults.value++ : rooms.value++;
const decrement = (type) => {
  if (type === 'adults' && adults.value > 1) adults.value--;
  if (type === 'rooms' && rooms.value > 1) rooms.value--;
};
const applyGuests = () => {
  guests.value = `${adults.value} adult${adults.value > 1 ? 's' : ''} · ${rooms.value} habitació${rooms.value > 1 ? 's' : ''}`;
  dropdownOpen.value = false;
};

// Búsqueda
const searchRooms = () => {
  router.push({
    path: '/habitaciones',
    query: {
      date_in: startDate.value,
      date_out: endDate.value,
      adults: adults.value,
      rooms: rooms.value
    }
  });
};
</script>

<style scoped>
:root {
  --background-main: #ffffff;
  --background-light: #f8f9fa;
  --primary-color: #cc7a39;
  --secondary-color: #FFA462;
  --text-primary: #212529;
  --text-secondary: #495057;
  --border-color: #dee2e6;
  --hover-color: #e9ecef;
}

.search-bar {
  background-color: var(--background-light);
  padding: 1rem;
  border-radius: 0.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  margin: 1rem;
}

.search-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.form-input {
  padding: 0.75rem;
  border: 1px solid var(--border-color);
  border-radius: 0.25rem;
  font-size: 0.875rem;
  color: var(--text-primary);
  background-color: var(--background-main);
  transition: border-color 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-color);
}

.search-button {
  background-color: var(--secondary-color);
  color: white;
  border: none;
  padding: 0.75rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.search-button:hover {
  background-color: var(--secondary-color);
}

.error-message {
  color: var(--status-error);
  font-size: 0.875rem;
  margin-top: 0.5rem;
}

/* Tablet (768px) */
@media (min-width: 48em) {
  .search-bar {
    margin: 2rem auto;
    max-width: 720px;
    padding: 1.5rem;
  }

  .search-form {
    flex-direction: row;
    align-items: flex-end;
  }

  .form-group {
    flex: 1;
  }

  .search-button {
    align-self: flex-end;
    min-width: 120px;
  }
}

/* Desktop (1024px) */
@media (min-width: 64em) {
  .search-bar {
    max-width: 960px;
    padding: 2rem;
  }

  .form-input,
  .search-button {
    font-size: 1rem;
  }
}

/* Large Desktop (1200px) */
@media (min-width: 75em) {
  .search-bar {
    max-width: 1140px;
  }
}

.search-container {
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: white;
  border-radius: 30px;
  padding: 10px;
  max-width: 1200px;
  margin: 20px auto;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.search-form {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
}

.form-group {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 10px 15px;
  background-color: #fff;
  border-radius: 30px;
  border: 1px solid #ddd;
  cursor: pointer;
  transition: all 0.3s ease-in-out;
}

.form-group:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.input-field {
  border: none;
  font-size: 16px;
  outline: none;
  flex: 1;
  background: transparent;
  padding: 8px 0;
}

select.input-field {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background: transparent;
}

.search-button-container {
  padding: 0;
  border: none;
}

.search-button {
  background-color: var(--secundary-color);
  color: white;
  border: none;
  padding: 12px 20px;
  font-size: 16px;
  border-radius: 50px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  font-weight: bold;
  width: 100%;
}

.search-button:hover {
  background-color: var(--primary-color);
}

@media (min-width: 768px) {
  .search-form {
    flex-direction: row;
    align-items: center;
  }

  .form-group {
    flex: 1;
  }

  .search-button-container {
    flex: 0 0 auto;
    width: auto;
  }

  .search-button {
    width: auto;
  }
}

.date-group,
.dropdown-group {
  position: relative;
}

.date-picker,
.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 5px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  z-index: 10;
  width: 100%;
}

.date-input {
  width: 100%;
  margin: 5px 0;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.dropdown-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dropdown-label {
  font-size: 14px;
}

.dropdown-controls {
  display: flex;
  align-items: center;
  gap: 10px;
}

.control-button {
  background-color: #f0f0f0;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.control-button:hover {
  background-color: #e0e0e0;
}

.dropdown-value {
  font-size: 14px;
  font-weight: bold;
}

.apply-button {
  background-color: var(--secundary-color);
  color: white;
  border: none;
  padding: 5px 10px;
  font-size: 14px;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 10px;
}

.apply-button:hover {
  background-color: var(--primary-color);
}
</style>