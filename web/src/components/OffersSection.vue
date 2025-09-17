<template>
  <section class="offers">
    <h2>{{ $t('offersTitle') }}</h2>
    <div class="carousel-container">
      <button class="carousel-arrow prev" @click="prevSlide">
        <i class="fas fa-chevron-left"></i>
      </button>
      <div class="carousel-track" :style="{ transform: `translateX(-${currentSlide * 33.333}%)` }">
        <div class="offer-card scroll-animate" v-for="(offer, index) in offers" :key="index">
          <img :src="offer.image" :alt="offer.title" class="offer-image">
          <div class="offer-content">
            <h3>{{ $t(`offer${index + 1}Title`) }}</h3>
            <p class="custom-paragraph">{{ $t(`offer${index + 1}Description`) }}</p>
          </div>
        </div>
      </div>
      <button class="carousel-arrow next" @click="nextSlide">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';

const currentSlide = ref(0);
const offers = ref([
  { image: '/images/long-stay.jpg' },
  { image: '/images/seasonal.jpg' },
  { image: '/images/groups.jpg' },
  { image: '/images/special-offer.jpg' },
  { image: '/images/weekend.jpg' }
]);

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % (offers.value.length - 2);
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + (offers.value.length - 2)) % (offers.value.length - 2);
};
</script>

<style scoped>
.offers {
  padding: var(--spacing-xl) var(--spacing-md);
  background-color: var(--background-main);
  text-align: center;
}

.carousel-container {
  position: relative;
  overflow: hidden;
  padding: 0 50px;
  max-width: 1200px;
  margin: 0 auto;
}

.carousel-track {
  display: flex;
  transition: transform 0.5s ease-in-out;
}

.offer-card {
  flex: 0 0 33.333%;
  padding: 15px;
  position: relative;
  min-height: 400px;
}

.offer-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: var(--border-radius-md);
}

.offer-content {
  position: absolute;
  bottom: 15px;
  left: 15px;
  right: 15px;
  padding: 2rem;
  background: linear-gradient(transparent, rgba(0,0,0,0.8));
  color: white;
  border-radius: 0 0 var(--border-radius-md) var(--border-radius-md);
}

.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  padding: 1.2rem;
  width: 50px;
  height: 50px;
  cursor: pointer;
  border-radius: 50%;
  z-index: 10;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
}

.carousel-arrow i {
  font-size: 24px;
  color: var(--primary-color);
  transition: transform 0.2s ease;
  
}

.carousel-arrow:hover {
  background: linear-gradient(145deg, var(--secondary-color), var(--primary-color));
  transform: translateY(-50%) scale(1.1);
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.carousel-arrow:hover i {
  transform: scale(1.2);
}

.carousel-arrow:active {
  transform: translateY(-50%) scale(0.95);
}

.prev { left: 83px; }
.next { right: 20px; }

/* Responsive */
@media (max-width: 768px) {
  .carousel-track {
    transform: none !important;
  }
  
  .offer-card {
    flex: 0 0 100%;
  }
  
  .carousel-arrow {
    display: none;
    width: 40px;
    height: 40px;
    padding: 0.8rem;
  }
  
  .carousel-arrow i {
    font-size: 20px;
  }
  
  .value-card {
    min-height: 300px;
  }
}

.custom-paragraph {
  color: var(--background-main);
}
</style>