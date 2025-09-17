<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const rooms = ref([]);

onMounted(async () => {
  const { destination, startDate, endDate, guests } = route.query;
  const response = await axios.get('/api/rooms', {
    params: { destination, startDate, endDate, guests },
  });
  rooms.value = response.data;
});
</script>

<template>
  <div>
    <h1>Habitacions disponibles</h1>
    <div v-if="rooms.length">
      <div v-for="room in rooms" :key="room.id">
        <h2>{{ room.name }}</h2>
        <p>{{ room.description }}</p>
        <!-- Más detalles de la habitación -->
      </div>
    </div>
    <div v-else>
      <p>No hi ha habitacions disponibles per a les dates seleccionades.</p>
    </div>
  </div>
</template>