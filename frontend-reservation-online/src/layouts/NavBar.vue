<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const authStore = useAuthStore()

const handleLogout = () => {
  authStore.logout()
}
</script>

<template>
  <nav class="w-full border-b border-gray-100 bg-white">
    <div
      class="mx-auto flex h-[76px] max-w-[1110px] items-center justify-between px-4"
    >
      <!-- Logo -->
      <RouterLink
        to="/"
        class="text-[24px] font-black tracking-[-1.5px] text-black"
      >
        Lodgify
      </RouterLink>

      <!-- Navigation conditionnelle selon le rôle -->
      <div class="flex items-center gap-2">
        <!-- 1. Menu pour l'utilisateur avec le rôle 'user' connecté -->
        <template v-if="authStore.isAuthenticated && authStore.isUser">
          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Salles</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Équipements</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Réservations</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Mon profil</span>
          </RouterLink>
        </template>

        <!-- 2. Menu pour Admin ou Responsable -->
        <template v-else-if="authStore.isAuthenticated && (authStore.isAdmin || authStore.isResponsable)">
          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Accueil</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Salles</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Équipements</span>
          </RouterLink>

          <!-- Raccourci Dashboard Admin -->
          <RouterLink
            v-if="authStore.isAdmin"
            to="/admin/home"
            class="rounded-xl bg-[#EEF2FF] px-4 py-2 text-[13px] font-bold text-[#3730A3] transition hover:bg-[#E0E7FF]"
          >
            Dashboard Admin
          </RouterLink>

          <!-- Raccourci Dashboard Responsable -->
          <RouterLink
            v-else-if="authStore.isResponsable"
            to="/responsable/home"
            class="rounded-xl bg-blue-100 px-4 py-2 text-[13px] font-bold text-blue-800 transition hover:bg-blue-200"
          >
            Dashboard Responsable
          </RouterLink>
        </template>

        <!-- 3. Menu pour Visiteur (non-connecté) -->
        <template v-else>
          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Accueil</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
          >
            <span>Salles</span>
          </RouterLink>

          <RouterLink
            to="/"
            class="flex cursor-pointer items-center gap-1 rounded-xl px-4 py-2 text-[14px] font-semibold text-black transition hover:bg-[#e6f0f6]"
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
              class="cursor-pointer rounded-xl border border-gray-200 bg-gray-50 px-4 py-2 text-[13px] font-bold text-red-600 transition hover:bg-red-50 hover:border-red-200"
            >
              Déconnexion
            </button>
          </div>
        </template>

        <!-- État Non-Connecté (Invité) -->
        <template v-else>
          <RouterLink
            to="/auth/login"
            class="rounded-xl bg-[#e5f1f8] px-5 py-2.5 text-[14px] font-bold text-black transition hover:bg-[#d9eaf3]"
          >
            Se connecter
          </RouterLink>

          <RouterLink
            to="/auth/register"
            class="rounded-xl bg-[#111111] px-5 py-2.5 text-[14px] font-bold text-white transition hover:bg-black"
          >
            S'inscrire
          </RouterLink>
        </template>
      </div>
    </div>
  </nav>
</template>



