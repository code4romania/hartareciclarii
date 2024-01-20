import { createRouter, createWebHistory } from 'vue-router'
import IndexPage from '/resources/js/components/IndexPage.vue';
import ProfilePage from '/resources/js/components/profilePage.vue';
import ResetPassword from '/resources/js/components/resetPassword.vue';
import Point from '/resources/js/components/point.vue';

const router = createRouter({
  history: createWebHistory(),
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
    },
    {
      path: '/reset/:token',
      name: 'resetPassword',
      component: ResetPassword
    },
    {
      path: '/point/:point_id',
      name: 'point',
      component: Point
    }
  ]
})

export default router
