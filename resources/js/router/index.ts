import { createRouter, createWebHistory } from 'vue-router'
import IndexPage from '/resources/js/components/IndexPage.vue';
import ProfilePage from '/resources/js/components/profilePage.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.APP_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: IndexPage
    },
    {
      path: '/profile',
      name: 'userProfile',
      component: ProfilePage
    }
  ]
})

export default router
