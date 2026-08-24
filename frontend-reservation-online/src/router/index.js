import Login from '@/views/auth/Login.vue'
import Register from '@/views/auth/Register.vue'
import { createRouter, createWebHistory } from 'vue-router'


const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [

        
        {
          path: '/auth/login',
          name: 'login',
          component: Login,
        },

        {
          path: '/auth/register',
          name: 'register',
          component: Register,
}],
})

export default router
