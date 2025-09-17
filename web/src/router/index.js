import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import LegalView from '../views/LegalView.vue';
import SearchView from '../views/SearchView.vue';
import HotelsView from '@/views/HotelsView.vue';
import ReservaView from '@/views/ReservaView.vue';
import PaymentView from '@/views/PaymentView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/legal',
      name: 'legal',
      component: LegalView,
    },
    {
      path: '/search',
      name: 'search',
      component: SearchView,
    },
    {
      path: '/search/hotels',
      name: 'hotels',
      component: HotelsView,
    },
    {
      path: '/payment',
      name: 'payment',
      component: PaymentView,
    },
    {
      path: '/reserva',
      name: 'reserva',
      component: ReservaView,
      props: route => ({ room: route.query.room }) // Pasar los datos del hotel como props
    }
  ],
});

export default router;