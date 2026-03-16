import { createRouter, createWebHistory } from 'vue-router';
import LandingView from '../views/LandingView.vue';
import GameView from '../views/GameView.vue';

const routes = [
  {
    path: '/',
    name: 'landing',
    component: LandingView
  },
  {
    path: '/game',
    name: 'game',
    component: GameView,
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token');
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'landing' });
  } else {
    next();
  }
});

export default router;
