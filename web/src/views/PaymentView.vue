<template>
  <div>
    <HeaderFixComponent />
    <PaymentComponent />
    <FooterComponent />
  </div>
</template>

<script setup>
import HeaderFixComponent from '@/components/HeaderFixComponent.vue'
import PaymentComponent from '@/components/PaymentComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
</script>

<style scoped>
.reserve-container {
  margin-top: 100px;
}
</style>
