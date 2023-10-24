import { createRouter, createWebHistory } from 'vue-router'
import IndexPage from '/resources/js/components/IndexPage.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.APP_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: IndexPage
    }
  ]
})

export default router
