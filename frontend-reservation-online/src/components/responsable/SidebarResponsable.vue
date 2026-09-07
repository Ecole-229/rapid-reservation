<script setup>
import { RouterLink, useRoute } from "vue-router";
import { useAuthStore } from "@/store/auth";
import { useResponsableReservationsStore } from "@/store/responsableReservations";
import { LayoutDashboard, Clock, CheckCircle2, XCircle, LogOut, Building2 } from "@lucide/vue";

const authStore = useAuthStore();
const store = useResponsableReservationsStore();
const route = useRoute();

function handleLogout() {
  authStore.logout();
}

const liens = [
  { nom: "responsable-home", label: "Réservations", icon: LayoutDashboard },
  { nom: "responsable-en-attente", label: "En attente", icon: Clock, compteurCle: "en_attente" },
  { nom: "responsable-confirmees", label: "Confirmées", icon: CheckCircle2, compteurCle: "confirmee" },
  { nom: "responsable-rejetees", label: "Rejetées", icon: XCircle, compteurCle: "rejetee" },
];
</script>

<template>
  <aside class="w-64 shrink-0 bg-white border-r border-slate-200 flex flex-col h-screen sticky top-0">
    <div class="h-16 flex items-center gap-2 px-6 border-b border-slate-200">
      <Building2 class="w-5 h-5 text-blue-600" />
      <span class="font-semibold text-slate-900">RapidRéservation</span>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
      <RouterLink v-for="lien in liens" :key="lien.nom" :to="{ name: lien.nom }"
        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition" :class="route.name === lien.nom
          ? 'text-blue-600 bg-blue-50'
          : 'text-slate-600 hover:bg-slate-50'">
        <span class="flex items-center gap-3">
          <component :is="lien.icon" class="w-4 h-4" />
          {{ lien.label }}
        </span>
        <span v-if="lien.compteurCle"
          class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
          {{ store.compteurs[lien.compteurCle] }}
        </span>
      </RouterLink>
    </nav>

    <div class="border-t border-slate-200 p-4">
      <div class="flex items-center gap-3 mb-3">
        <div
          class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-semibold text-sm">
          {{ authStore.currentUser?.nom?.charAt(0)?.toUpperCase() || "R" }}
        </div>
        <div class="flex flex-col min-w-0">
          <span class="text-sm font-semibold text-slate-900 truncate">
            {{ authStore.currentUser?.nom || "Responsable" }}
          </span>
          <span class="text-xs text-slate-500 capitalize">{{ authStore.userRole }}</span>
        </div>
      </div>

      <button type="button"
        class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-sm font-medium text-red-600 border border-red-200 hover:bg-red-50 transition"
        @click="handleLogout">
        <LogOut class="w-4 h-4" />
        Déconnexion
      </button>
    </div>
  </aside>
</template>
