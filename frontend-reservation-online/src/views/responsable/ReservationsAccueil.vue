<script setup>
import { ref } from "vue";
import { Plus } from "@lucide/vue";
import StatsBar from "@/components/responsable/StatsBar.vue";
import ReservationsTable from "@/components/responsable/ReservationsTable.vue";
import CreerReservationModal from "@/components/responsable/CreerReservationModal.vue";
import { useResponsableReservationsStore } from "@/store/responsableReservations";

const store = useResponsableReservationsStore();
const modaleCreationOuverte = ref(false);
</script>

<template>
  <div>
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-xl font-semibold text-slate-900">Réservations</h1>
        <p class="mt-1 text-sm text-slate-500">Vue d'ensemble de toutes les réservations.</p>
      </div>
      <button type="button"
        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition"
        @click="modaleCreationOuverte = true">
        <Plus class="w-4 h-4" />
        Nouvelle réservation
      </button>
    </div>

    <div v-if="store.errorMessage"
      class="mb-6 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
      {{ store.errorMessage }}
    </div>

    <div v-if="store.loading" class="py-20 text-center text-slate-400 text-sm">
      Chargement des réservations…
    </div>

    <template v-else>
      <StatsBar :compteurs="store.compteurs" />
      <ReservationsTable :reservations="store.parStatut('toutes')" :show-actions="false" />
    </template>

    <CreerReservationModal :open="modaleCreationOuverte" @fermer="modaleCreationOuverte = false" />
  </div>
</template>
