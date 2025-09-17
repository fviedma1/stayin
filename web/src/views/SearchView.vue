<script setup>
import HeaderComponent from '@/components/HeaderComponent.vue';
import HeroSection from '@/components/HeroSection.vue';
import SearchBar from '@/components/SearchBar.vue';
import FooterComponent from '@/components/FooterComponent.vue';
</script>

<template>
  <div>
    <HeaderComponent />
    <HeroSection />
    <div class="search-container">
      <SearchBar />
    </div>
      <FooterComponent />
  </div>
</template>


<style scoped>
.search-container {
  padding: 20px;
  max-width: 1200px;
  margin: 0 auto;
}
</style>