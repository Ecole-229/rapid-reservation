import Dashbord from '@/views/admin/layouts/Dashbord.vue'
import Equipements from '@/views/admin/layouts/Equipements.vue'
import Galeries from '@/views/admin/layouts/Galeries.vue'
import Reservations from '@/views/admin/layouts/Reservations.vue'
import Salles from '@/views/admin/layouts/Salles.vue'
import Users from '@/views/admin/layouts/Users.vue'
import Login from '@/views/auth/Login.vue'
import Register from '@/views/auth/Register.vue'
import Home from '@/views/Home.vue'
import AppResponsable from '@/views/responsable/AppResponsable.vue'
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home,
  },
  {
    path: '/admin/users',
    name: 'admin-users',
    component: Users,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admim/dashboard',
    name: 'admin-dashboard',
    component: Dashbord,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/reservations',
    name: 'admin-reservations',
    component: Reservations,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/salles',
    name: 'admin-salles',
    component: Salles,
    meta: { requiresAuth: true, role: 'admin' },
  },

  {
    path: '/admin/equipments',
    name: 'admin-equipments',
    component: Equipements,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/galeries',
    name: 'admin-galeries',
    component: Galeries,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/responsable/home',
    name: 'responsable-home',
    component: AppResponsable,
    meta: { requiresAuth: true, role: 'responsable' },
  },
  {
    path: '/auth/login',
    name: 'login',
    component: Login,
    meta: { guestOnly: true },
  },
  {
    path: '/auth/register',
    name: 'register',
    component: Register,
    meta: { guestOnly: true },
  },
  // Redirection par défaut pour les routes inexistantes
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

// Navigation Guards pour la protection des routes
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  let user = null
  try {
    const rawUser = localStorage.getItem('user')
    if (rawUser && rawUser !== 'undefined') {
      user = JSON.parse(rawUser)
    }
  } catch (e) {
    console.error('Erreur parsing user dans router guard', e)
  }

  // 1. Si la route requiert d'être invité (login/register) et que l'utilisateur est déjà connecté
  if (to.meta.guestOnly && token) {
    if (user?.role === 'admin') {
      return next({ name: 'admin-users' })
    } else if (user?.role === 'responsable') {
      return next({ name: 'responsable-home' })
    }
    return next({ name: 'home' })
  }

  // 2. Si la route requiert une authentification et qu'aucun token n'est présent
  if (to.meta.requiresAuth && !token) {
    return next({ name: 'login' })
  }

  // 3. Vérification du rôle requis
  if (to.meta.role && user?.role !== to.meta.role) {
    // Si l'utilisateur n'a pas le bon rôle, on le redirige selon son rôle réel
    if (user?.role === 'admin') {
      return next({ name: 'admin-users' })
    } else if (user?.role === 'responsable') {
      return next({ name: 'responsable-home' })
    }
    return next({ name: 'home' })
  }

  return next()
})

export default router

