<script setup>
import { ref, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import { Menu, X } from 'lucide-vue-next'

const authStore = useAuthStore()
const route = useRoute()

const isMenuOpen = ref(false)

const handleLogout = () => {
  authStore.logout()
  isMenuOpen.value = false
}

const closeMenu = () => {
  isMenuOpen.value = false
}

// Ferme le menu à chaque changement de route
watch(() => route.path, () => {
  isMenuOpen.value = false
})
</script>

<template>
  <div class="fixed inset-x-0 top-4 z-50 flex justify-center px-4">
    <nav class="w-full max-w-[1110px] rounded-full border border-white/40 bg-white/60 shadow-[0_8px_32px_rgba(15,23,42,0.08)] backdrop-blur-xl">
      <div class="mx-auto flex h-[64px] items-center justify-between px-5 sm:px-6">

        <!-- Logo -->
        <RouterLink
          to="/"
          class="font-bricolage text-[22px] sm:text-[24px] font-black tracking-[-1.5px] text-black flex-shrink-0"
          @click="closeMenu"
        >
          Lodgify
        </RouterLink>

        <!-- Navigation desktop -->
        <div class="hidden md:flex items-center gap-1">
          <!-- Utilisateur connecté -->
          <template v-if="authStore.isAuthenticated && authStore.isUser">
            <RouterLink to="/salles"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70"
              :class="{ 'bg-white/70': $route.path.startsWith('/salles') }">Salles</RouterLink>
            <RouterLink to="/equipements"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70"
              :class="{ 'bg-white/70': $route.path.startsWith('/equipements') }">Équipements</RouterLink>
            <RouterLink to="/reservations"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70"
              :class="{ 'bg-white/90': $route.path.startsWith('/reservations') }">Réservations</RouterLink>
            <RouterLink to="/"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70">Mon profil</RouterLink>
          </template>

          <!-- Admin / Responsable -->
          <template v-else-if="authStore.isAuthenticated && (authStore.isAdmin || authStore.isResponsable)">
            <RouterLink to="/"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70">Accueil</RouterLink>
            <RouterLink to="/salles"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70">Salles</RouterLink>
            <RouterLink to="/equipements"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70">Équipements</RouterLink>
          </template>

          <!-- Visiteur -->
          <template v-else>
            <RouterLink to="/"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70">Accueil</RouterLink>
            <RouterLink to="/salles"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70">Salles</RouterLink>
            <RouterLink to="/equipements"
              class="flex cursor-pointer items-center gap-1 rounded-full px-3 py-2 text-[13px] font-semibold text-black transition hover:bg-white/70">Équipements</RouterLink>
          </template>
        </div>

        <!-- Actions Auth desktop -->
        <div class="hidden md:flex items-center gap-3">
          <template v-if="authStore.isAuthenticated">
            <div class="flex items-center gap-3">
              <div class="flex flex-col text-right">
                <span class="text-[13px] font-bold text-black leading-tight">{{ authStore.currentUser?.nom || 'Utilisateur' }}</span>
                <span class="text-[11px] capitalize text-gray-500">{{ authStore.userRole }}</span>
              </div>
              <button @click="handleLogout"
                class="cursor-pointer rounded-full border border-gray-200/70 bg-white/50 px-4 py-2 text-[13px] font-bold text-red-600 transition hover:bg-red-50 hover:border-red-200">
                Déconnexion
              </button>
            </div>
          </template>
          <template v-else>
            <RouterLink to="/auth/login"
              class="rounded-full bg-white/70 px-4 py-2 text-[13px] font-bold text-black transition hover:bg-white">
              Se connecter
            </RouterLink>
            <RouterLink to="/auth/register"
              class="rounded-full bg-[#111111] px-4 py-2 text-[13px] font-bold text-white transition hover:bg-black">
              S'inscrire
            </RouterLink>
          </template>
        </div>

        <!-- Burger button mobile -->
        <button
          @click="isMenuOpen = !isMenuOpen"
          class="md:hidden flex h-10 w-10 items-center justify-center rounded-full bg-white/60 text-black border border-white/50 transition hover:bg-white"
          :aria-label="isMenuOpen ? 'Fermer le menu' : 'Ouvrir le menu'"
        >
          <X v-if="isMenuOpen" :size="20" />
          <Menu v-else :size="20" />
        </button>

      </div>
    </nav>

    <!-- Drawer mobile -->
    <Transition name="drawer">
      <div
        v-if="isMenuOpen"
        class="md:hidden absolute top-[72px] left-0 right-0 mx-4 rounded-[20px] border border-white/40 bg-white/90 shadow-[0_16px_48px_rgba(15,23,42,0.12)] backdrop-blur-xl overflow-hidden"
      >
        <div class="p-4 flex flex-col gap-1">

          <!-- Utilisateur connecté -->
          <template v-if="authStore.isAuthenticated && authStore.isUser">
            <!-- Profil en-tête -->
            <div class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-2xl bg-white/60 border border-gray-100">
              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white text-[13px] font-bold shrink-0">
                {{ (authStore.currentUser?.nom || 'U')[0].toUpperCase() }}
              </div>
              <div>
                <p class="text-[13px] font-bold text-black leading-tight">{{ authStore.currentUser?.nom || 'Utilisateur' }}</p>
                <p class="text-[11px] text-gray-500 capitalize">{{ authStore.userRole }}</p>
              </div>
            </div>

            <RouterLink to="/salles" @click="closeMenu"
              class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black transition hover:bg-white/70"
              :class="{ 'bg-white/80': $route.path.startsWith('/salles') }">Salles</RouterLink>
            <RouterLink to="/equipements" @click="closeMenu"
              class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black transition hover:bg-white/70"
              :class="{ 'bg-white/80': $route.path.startsWith('/equipements') }">Équipements</RouterLink>
            <RouterLink to="/reservations" @click="closeMenu"
              class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black transition hover:bg-white/70"
              :class="{ 'bg-white/80': $route.path.startsWith('/reservations') }">Réservations</RouterLink>
            <RouterLink to="/" @click="closeMenu"
              class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black transition hover:bg-white/70">Mon profil</RouterLink>

            <div class="mt-2 border-t border-gray-100 pt-2">
              <button @click="handleLogout"
                class="w-full rounded-xl px-4 py-3 text-left text-[14px] font-bold text-red-600 transition hover:bg-red-50 cursor-pointer">
                Déconnexion
              </button>
            </div>
          </template>

          <!-- Admin / Responsable -->
          <template v-else-if="authStore.isAuthenticated && (authStore.isAdmin || authStore.isResponsable)">
            <div class="flex items-center gap-3 px-3 py-2.5 mb-1 rounded-2xl bg-white/60 border border-gray-100">
              <div class="h-9 w-9 rounded-full bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center text-white text-[13px] font-bold shrink-0">
                {{ (authStore.currentUser?.nom || 'A')[0].toUpperCase() }}
              </div>
              <div>
                <p class="text-[13px] font-bold text-black">{{ authStore.currentUser?.nom || 'Admin' }}</p>
                <p class="text-[11px] text-gray-500 capitalize">{{ authStore.userRole }}</p>
              </div>
            </div>
            <RouterLink to="/" @click="closeMenu" class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black hover:bg-white/70">Accueil</RouterLink>
            <RouterLink to="/salles" @click="closeMenu" class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black hover:bg-white/70">Salles</RouterLink>
            <RouterLink to="/equipements" @click="closeMenu" class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black hover:bg-white/70">Équipements</RouterLink>
            <div class="mt-2 border-t border-gray-100 pt-2">
              <button @click="handleLogout" class="w-full rounded-xl px-4 py-3 text-left text-[14px] font-bold text-red-600 hover:bg-red-50 cursor-pointer">Déconnexion</button>
            </div>
          </template>

          <!-- Visiteur -->
          <template v-else>
            <RouterLink to="/" @click="closeMenu" class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black hover:bg-white/70">Accueil</RouterLink>
            <RouterLink to="/salles" @click="closeMenu" class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black hover:bg-white/70">Salles</RouterLink>
            <RouterLink to="/equipements" @click="closeMenu" class="flex items-center rounded-xl px-4 py-3 text-[14px] font-semibold text-black hover:bg-white/70">Équipements</RouterLink>
            <div class="mt-2 border-t border-gray-100 pt-2 flex flex-col gap-2">
              <RouterLink to="/auth/login" @click="closeMenu"
                class="w-full rounded-xl bg-white/60 px-4 py-3 text-center text-[14px] font-bold text-black border border-gray-200 hover:bg-white transition">
                Se connecter
              </RouterLink>
              <RouterLink to="/auth/register" @click="closeMenu"
                class="w-full rounded-xl bg-[#111111] px-4 py-3 text-center text-[14px] font-bold text-white hover:bg-black transition">
                S'inscrire
              </RouterLink>
            </div>
          </template>

        </div>
      </div>
    </Transition>
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

/* ================================================================
   ANIMATION DRAWER MOBILE
================================================================ */

.drawer-enter-active {
    transition: opacity 0.22s ease, transform 0.22s cubic-bezier(0.22, 1, 0.36, 1);
}

.drawer-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}

.drawer-enter-from,
.drawer-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
}
</style>
