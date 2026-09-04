<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()

const handleLogout = () => {
  authStore.logout()
}
</script>

<template>
  <div class="fixed inset-x-0 top-4 z-50 flex justify-center px-4">
    <nav
      class="w-full max-w-[1110px]
             rounded-full
             border border-white/40
             bg-white/60
             shadow-[0_8px_32px_rgba(15,23,42,0.08)]
             backdrop-blur-xl
             backdrop-saturate-150"
    >
    <div
      class="mx-auto flex h-[64px] items-center justify-between px-6"
    >
      <!-- Logo -->
      <RouterLink
        to="/"
        class="font-bricolage text-[24px] font-black tracking-[-1.5px] text-black"
      >
        Lodgify
      </RouterLink>

      <!-- Navigation conditionnelle selon le rôle -->
      <div class="flex items-center gap-2">
        <!-- 1. Menu pour l'utilisateur avec le rôle 'user' connecté -->
        <template v-if="authStore.isAuthenticated && authStore.isUser">
          <RouterLink
            to="/salles"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Salles</span>
          </RouterLink>

          <RouterLink
            to="/equipements"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Équipements</span>
          </RouterLink>

          <RouterLink
            to="/reservations"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
            :class="{ 'bg-white/90 shadow-sm text-indigo-600': $route.path.startsWith('/reservations') }"
          >
            <span>Réservations</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Mon profil</span>
          </RouterLink>
        </template>

        <!-- 2. Menu pour Admin ou Responsable -->
        <template v-else-if="authStore.isAuthenticated && (authStore.isAdmin || authStore.isResponsable)">
          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Accueil</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Salles</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Équipements</span>
          </RouterLink>

          <!-- Raccourci Dashboard Admin -->
          <RouterLink
            v-if="authStore.isAdmin"
            to="/admin/dashboard"
            class="rounded-full bg-[#EEF2FF]/80 px-4 py-2 text-[13px] font-bold text-[#3730A3] transition hover:bg-[#E0E7FF]"
          >
            Dashboard Admin
          </RouterLink>

          <!-- Raccourci Dashboard Responsable -->
          <RouterLink
            v-else-if="authStore.isResponsable"
            to="/responsable/home"
            class="rounded-full bg-blue-100/80 px-4 py-2 text-[13px] font-bold text-blue-800 transition hover:bg-blue-200"
          >
            Dashboard Responsable
          </RouterLink>
        </template>

        <!-- 3. Menu pour Visiteur (non-connecté) -->
        <template v-else>
          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Accueil</span>
          </RouterLink>

          <RouterLink
            to="/salles"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Salles</span>
          </RouterLink>

          <RouterLink
            to="/equipements"
            class="flex cursor-pointer items-center gap-1 rounded-full px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-white/70"
          >
            <span>Équipements</span>
          </RouterLink>
        </template>
      </div>

      <!-- Actions Auth (Droite) -->
      <div class="flex items-center gap-4">
        <!-- État Connecté -->
        <template v-if="authStore.isAuthenticated">
          <div class="flex items-center gap-3">
            <div class="flex flex-col text-right">
              <span class="text-[14px] font-bold text-black">
                {{ authStore.currentUser?.nom || 'Utilisateur' }}
              </span>
              <span class="text-[11px] capitalize text-gray-500">
                {{ authStore.userRole }}
              </span>
            </div>

            <button
              @click="handleLogout"
              class="cursor-pointer rounded-full border border-gray-200/70 bg-white/50 px-4 py-2 text-[13px] font-bold text-red-600 transition hover:bg-red-50 hover:border-red-200"
            >
              Déconnexion
            </button>
          </div>
        </template>

        <!-- État Non-Connecté (Invité) -->
        <template v-else>
          <RouterLink
            to="/auth/login"
            class="rounded-full bg-white/70 px-5 py-2.5 text-[14px] font-bold text-black transition hover:bg-white"
          >
            Se connecter
          </RouterLink>

          <RouterLink
            to="/auth/register"
            class="rounded-full bg-[#111111] px-5 py-2.5 text-[14px] font-bold text-white transition hover:bg-black"
          >
            S'inscrire
          </RouterLink>
        </template>
      </div>
    </div>
    </nav>
  </div>
</template>


<style scoped>

/* ================================================================
   FILET DE SECURITE - indépendant de Tailwind
   Garantit l'effet verre dépoli et le centrage même si les
   classes Tailwind ne sont pas correctement générées.
================================================================ */

.fixed {
    position: fixed;
    left: 0;
    right: 0;
    top: 16px;
    z-index: 50;
    display: flex;
    justify-content: center;
    padding-left: 1rem;
    padding-right: 1rem;
}

nav {
    width: 100%;
    max-width: 1110px;
    border-radius: 9999px;
    border: 1px solid rgba(255, 255, 255, 0.4);
    background-color: rgba(255, 255, 255, 0.6);
    box-shadow: 0 8px 32px rgba(15, 23, 42, 0.08);
    -webkit-backdrop-filter: blur(20px) saturate(150%);
    backdrop-filter: blur(20px) saturate(150%);
}
</style>
