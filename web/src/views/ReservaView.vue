<script setup>
import HeaderFixComponent from '@/components/HeaderFixComponent.vue';
import ReservationSummaryComponent from '@/components/ReservationSummaryComponent.vue';
import FooterComponent from '@/components/FooterComponent.vue';

const room = sessionStorage.getItem('selectedRoom');
const username = sessionStorage.getItem('username');
</script>

<template>
  <div>
    <HeaderFixComponent />
    <ReservationSummaryComponent :room="room" :username="username" />
    <FooterComponent />
  </div>
</template>

<style scoped>
/* Puedes añadir estilos específicos para esta vista aquí */
</style>