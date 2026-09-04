import CreateSalle from '@/views/admin/CreateSalle.vue'
import CreateUser from '@/views/admin/CreateUser.vue'
import InfosSalle from '@/views/admin/InfosSalle.vue'
import InfosUser from '@/views/admin/InfosUser.vue'
import UpdateSalle from '@/views/admin/UpdateSalle.vue'
import UpdateUser from '@/views/admin/UpdateUser.vue'
import Dashbord from '@/views/admin/layouts/Dashbord.vue'
import Calendar from '@/views/admin/layouts/Calendar.vue'
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
import CreateEquipement from '@/views/admin/CreateEquipement.vue'
import UpdateEquipement from '@/views/admin/UpdateEquipement.vue'
import InfosEquipement from '@/views/admin/InfosEquipement.vue'
import CreateImage from '@/views/admin/CreateImage.vue'
import UpdateImage from '@/views/admin/UpdateImage.vue'
import InfosImage from '@/views/admin/InfosImage.vue'
import CreateReservation from '@/views/admin/CreateReservation.vue'
import UpdateReservation from '@/views/admin/UpdateReservation.vue'
import InfosReservation from '@/views/admin/InfosReservation.vue'
import SallesUser from '@/views/SallesUser.vue'
import EquipementsUser from '@/views/EquipementsUser.vue'
import UserInfoEquipements from '@/views/UserInfoEquipements.vue'
import UserInfosSalle from '@/views/UserInfosSalle.vue'
import UserCreateReservation from '@/views/UserCreateReservation.vue'
import UserReservations from '@/views/UserReservations.vue'
import InfoReservation from '@/views/InfoReservation.vue'
import UserUpdateReservation from '@/views/UserUpdateReservation.vue'

const routes = [

  {
    path: '/salles',
    name: 'salles',
    component: SallesUser,
  },

  {
    path: '/salles/:id',
    name: 'info-user-salle',
    component: UserInfosSalle,
  },

  {
    path: '/equipements',
    name: 'equipements',
    component: EquipementsUser,
  },

  {
    path: '/equipements/:id',
    name: 'info-user-equipement',
    component: UserInfoEquipements,
  },

  {
    path: '/reserver',
    name: 'user-create-reservation',
    component: UserCreateReservation,
    meta: { requiresAuth: true },
  },

  {
    path: '/reservations',
    name: 'user-reservations',
    component: UserReservations,
    meta: { requiresAuth: true },
  },

  {
    path: '/reservations/:id',
    name: 'user-reservation-details',
    component: InfoReservation,
    meta: { requiresAuth: true },
  },

  {
    path: '/reservations/:id/modifier',
    name: 'user-update-reservation',
    component: UserUpdateReservation,
    meta: { requiresAuth: true },
  },


  {
    path: '/',
    name: 'home',
    component: Home,
  },

  // ==========================================
  // ADMIN — UTILISATEURS
  // ==========================================
  {
    path: '/admin/users',
    name: 'admin-users',
    component: Users,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/create-user',
    name: 'create-user',
    component: CreateUser,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/update-user/:id',
    name: 'update-user',
    component: UpdateUser,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/info-user/:id',
    name: 'info-user',
    component: InfosUser,
    meta: { requiresAuth: true, role: 'admin' },
  },

  // ==========================================
  // ADMIN — SALLES
  // ==========================================
  {
    path: '/admin/salles',
    name: 'admin-salles',
    component: Salles,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/create-salle',
    name: 'create-salle',
    component: CreateSalle,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/update-salle/:id',
    name: 'update-salle',
    component: UpdateSalle,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/info-salle/:id',
    name: 'info-salle',
    component: InfosSalle,
    meta: { requiresAuth: true, role: 'admin' },
  },

  // ==========================================
  // ADMIN — DASHBORD
  // ==========================================

  {
    path: '/admin/dashboard',
    name: 'admin-dashboard',
    component: Dashbord,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/calendar',
    name: 'admin-calendar',
    component: Calendar,
    meta: { requiresAuth: true, role: 'admin' },
  },

  // ==========================================
  // ADMIN — RESERVATIONS
  // ==========================================
  {
    path: '/admin/reservations',
    name: 'admin-reservations',
    component: Reservations,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/create-reservation',
    name: 'create-reservation',
    component: CreateReservation,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/update-reservation/:id',
    name: 'update-reservation',
    component: UpdateReservation,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/info-reservation/:id',
    name: 'info-reservation',
    component: InfosReservation,
    meta: { requiresAuth: true, role: 'admin' },
  },

  // ==========================================
  // ADMIN — EQUIPEMENTS
  // ==========================================

  {
    path: '/admin/equipments',
    name: 'admin-equipments',
    component: Equipements,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/create-equipment',
    name: 'create-equipment',
    component: CreateEquipement,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/update-equipment/:id',
    name: 'update-equipment',
    component: UpdateEquipement,
    meta: { requiresAuth: true, role: 'admin' },
  },
  {
    path: '/admin/info-equipment/:id',
    name: 'info-equipment',
    component: InfosEquipement,
    meta: { requiresAuth: true, role: 'admin' },
  },


  // ==========================================
  // ADMIN — GALERIES
  // ==========================================

  {
    path: '/admin/galeries',
    name: 'admin-galeries',
    component: Galeries,
    meta: { requiresAuth: true, role: 'admin' },
  },

  {
    path: '/admin/create-image',
    name: 'create-image',
    component: CreateImage,
    meta: { requiresAuth: true, role: 'admin' },
  },

  {
    path: '/admin/update-image/:id',
    name: 'update-image',
    component: UpdateImage,
    meta: { requiresAuth: true, role: 'admin' },
  },

  {
    path: '/admin/info-image/:id',
    name: 'info-image',
    component: InfosImage,
    meta: { requiresAuth: true, role: 'admin' },
  },

  // ==========================================
  // RESPONSABLE
  // ==========================================
  {
    path: '/responsable/home',
    name: 'responsable-home',
    component: AppResponsable,
    meta: { requiresAuth: true, role: 'responsable' },
  },

  // ==========================================
  // AUTH
  // ==========================================
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
