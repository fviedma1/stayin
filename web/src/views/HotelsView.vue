<template>
  <div>
    <HeaderFixComponent />
    <SearchBar @search="handleSearch" />
    <HotelsComponent ref="hotelsComponent" />
    <FooterComponent />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import HeaderFixComponent from '@/components/HeaderFixComponent.vue';
import HotelsComponent from '@/components/HotelsComponent.vue';
import FooterComponent from '@/components/FooterComponent.vue';
import SearchBar from '@/components/SearchBar.vue';

const hotelsComponent = ref(null);

const handleSearch = (searchParams) => {
  if (hotelsComponent.value) {
    hotelsComponent.value.updateSearchParams(searchParams);
  }
};
</script>

<style scoped>
.search-container {
  margin-top: 100px;
}
</style>