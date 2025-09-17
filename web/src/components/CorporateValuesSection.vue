<template>
  <section class="corporate-values">
    <div class="values-container">
      <h2>{{ $t('corporateValuesTitle') }}</h2>
      <p>{{ $t('corporateValuesDescription') }}</p>
      <div class="values-grid">
        <div class="value-card scroll-animate" v-for="(value, index) in values" :key="index">
          <div class="value-image" :style="{ backgroundImage: `url(${value.image})` }"></div>
          <div class="value-content">
            <h3>{{ $t(value.title) }}</h3>
            <p class="custom-paragraph">{{ $t(value.description) }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const values = ref([
  { image: '/images/sustainability.jpg', title: 'value.value1Title', description: 'value.value1Description' },
  { image: '/images/service.jpg', title: 'value.value2Title', description: 'value.value2Description' },
  { image: '/images/innovation.jpg', title: 'value.value3Title', description: 'value.value3Description' }
]);

onMounted(() => {
  const scrollElements = document.querySelectorAll('.scroll-animate');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      entry.target.classList.toggle('visible', entry.isIntersecting);
    });
  }, { threshold: 0.1 });
  scrollElements.forEach((el) => observer.observe(el));
});
</script>

<style scoped>
.corporate-values {
  padding: var(--spacing-xl) var(--spacing-md);
  background-color: var(--background-main);
  text-align: center;
}

.values-container {
  max-width: 1200px;
  margin: 0 auto;
}

.values-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: var(--spacing-lg);
}

.value-card {
  position: relative;
  overflow: hidden;
  border-radius: var(--border-radius-md);
  min-height: 400px;
  background-color: var(--background-main);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, opacity 0.5s ease-in-out;
  opacity: 0;
  transform: translateY(20px);
}

.value-image {
  height: 100%;
  width: 100%;
  background-size: cover;
  background-position: center;
  transition: transform 0.3s ease;
}

.value-card:hover {
  transform: translateY(-10px);
}

.value-card:hover .value-image {
  transform: scale(1.05);
}

.value-content {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 2rem;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  color: white;
}

.custom-paragraph {
  color: var(--background-main);
}

.scroll-animate.visible {
  opacity: 1;
  transform: translateY(0);
  transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
}

@media (prefers-reduced-motion: no-preference) {
  :root {
    view-transition-name: fade;
  }

  .value-card {
    view-transition-name: slide;
  }
}
</style>
