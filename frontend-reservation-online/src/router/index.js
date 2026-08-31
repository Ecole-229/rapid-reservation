import AppAdmin from "@/views/admin/AppAdmin.vue";
import Login from "@/views/auth/Login.vue";
import Register from "@/views/auth/Register.vue";
import Home from "@/views/Home.vue";
import ResponsableLayout from "@/views/responsable/ResponsableLayout.vue";
import ReservationsAccueil from "@/views/responsable/ReservationsAccueil.vue";
import ReservationsEnAttente from "@/views/responsable/ReservationsEnAttente.vue";
import ReservationsConfirmees from "@/views/responsable/ReservationsConfirmees.vue";
import ReservationsRejetees from "@/views/responsable/ReservationsRejetees.vue";
import { createRouter, createWebHistory } from "vue-router";

const routes = [
  {
    path: "/",
    name: "home",
    component: Home,
  },
  {
    path: "/admin/home",
    name: "admin-home",
    component: AppAdmin,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/responsable",
    component: ResponsableLayout,
    meta: { requiresAuth: true, role: "responsable" },
    children: [
      { path: "home", name: "responsable-home", component: ReservationsAccueil },
      {
        path: "reservations/en-attente",
        name: "responsable-en-attente",
        component: ReservationsEnAttente,
      },
      {
        path: "reservations/confirmees",
        name: "responsable-confirmees",
        component: ReservationsConfirmees,
      },
      {
        path: "reservations/rejetees",
        name: "responsable-rejetees",
        component: ReservationsRejetees,
      },
    ],
  },
  {
    path: "/auth/login",
    name: "login",
    component: Login,
    meta: { guestOnly: true },
  },
  {
    path: "/auth/register",
    name: "register",
    component: Register,
    meta: { guestOnly: true },
  },
  {
    path: "/:pathMatch(.*)*",
    redirect: "/",
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token");
  let user = null;
  try {
    const rawUser = localStorage.getItem("user");
    if (rawUser && rawUser !== "undefined") {
      user = JSON.parse(rawUser);
    }
  } catch (e) {
    console.error("Erreur parsing user dans router guard", e);
  }

  if (to.meta.guestOnly && token) {
    if (user?.role === "admin") return next({ name: "admin-home" });
    if (user?.role === "responsable") return next({ name: "responsable-home" });
    return next({ name: "home" });
  }

  if (to.meta.requiresAuth && !token) {
    return next({ name: "login" });
  }

  if (to.meta.role && user?.role !== to.meta.role) {
    if (user?.role === "admin") return next({ name: "admin-home" });
    if (user?.role === "responsable") return next({ name: "responsable-home" });
    return next({ name: "home" });
  }

  return next();
});

export default router;
