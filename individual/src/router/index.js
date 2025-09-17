import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import RoomsComponent from '../components/RoomsComponent.vue';
import NoticiasView from '../views/NoticiasView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/habitaciones',
      name: 'rooms',
      component: RoomsComponent,
      props: (route) => ({
        query: {
          roomType: route.query.roomType,
          date_in: route.query.date_in,
          date_out: route.query.date_out,
          adults: route.query.adults,
          rooms: route.query.rooms
        }
      })
    },
    {
      path: '/feedback/:token',
      name: 'feedback',
      component: () => import('../components/FeedbackFormComponent.vue'),
      props: true
    },
    {
      path: '/noticies',
      name: 'noticies',
      component: NoticiasView,
      meta: {
        title: 'Notícies - Hotel',
        metaTags: [
          {
            name: 'description',
            content: 'Descobreix les últimes notícies i novetats del nostre hotel'
          },
          {
            property: 'og:title',
            content: 'Notícies - Hotel'
          },
          {
            property: 'og:description',
            content: 'Descobreix les últimes notícies i novetats del nostre hotel'
          }
        ]
      }
    },
  ],
});

export default router;