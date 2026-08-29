<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminEquipementsStore } from '@/store/adminEquipements'
import {
  ArrowLeft,
  Pencil,
  Server,
  Boxes,
  Calendar,
  Info,
  Loader2,
  CheckCircle2,
  XCircle,
} from 'lucide-vue-next'

const route = useRoute()
const adminEquipementsStore = useAdminEquipementsStore()

const equipementId = route.params.id
const equipement = ref(null)
const isFetching = ref(true)

onMounted(async () => {
  try {
    equipement.value = await adminEquipementsStore.fetchEquipement(equipementId)
  } catch (error) {
    console.error('Erreur chargement équipement :', error)
  } finally {
    isFetching.value = false
  }
})

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: 'long',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(dateString))
  } catch {
    return dateString
  }
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-5xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-equipments' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des équipements</span>
        </RouterLink>

        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">
              Fiche de l'équipement
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Détails, stock en temps réel et historique de l'équipement.
            </p>
          </div>

          <RouterLink
            v-if="equipement"
            :to="{ name: 'update-equipment', params: { id: equipementId } }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
          >
            <Pencil :size="16" />
            <span>Modifier</span>
          </RouterLink>
        </div>
      </div>

      <!-- CHARGEMENT -->
      <div
        v-if="isFetching"
        class="flex flex-col items-center justify-center rounded-2xl border border-gray-100 bg-white p-16 shadow-sm"
      >
        <Loader2 :size="32" class="animate-spin text-blue-600" />
        <p class="mt-3 text-sm text-gray-500">Chargement de la fiche...</p>
      </div>

      <!-- ERREUR -->
      <div
        v-else-if="adminEquipementsStore.errorMessage && !equipement"
        class="rounded-2xl border border-red-200 bg-red-50 p-8 text-center text-sm text-red-700"
      >
        <XCircle :size="32" class="mx-auto mb-3 text-red-400" />
        <p class="font-semibold">Équipement introuvable</p>
        <p class="mt-1">{{ adminEquipementsStore.errorMessage }}</p>
      </div>

      <!-- CONTENU -->
      <div v-else-if="equipement" class="space-y-6">
        <!-- CARTE PRINCIPALE -->
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
          <!-- Header coloré -->
          <div class="flex flex-col sm:flex-row sm:items-center gap-5 bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
            <div
              v-if="equipement.image_url"
              class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-2xl border-2 border-white/40 bg-white"
            >
              <img
                :src="equipement.image_url"
                :alt="equipement.nom"
                class="h-full w-full object-cover"
              />
            </div>
            <div
              v-else
              class="flex h-20 w-20 flex-shrink-0 items-center justify-center rounded-2xl bg-white/20 text-white"
            >
              <Server :size="36" />
            </div>

            <div>
              <h2 class="text-xl font-bold text-white">
                {{ equipement.nom }}
              </h2>
              <div class="mt-2 flex items-center gap-2">
                <span
                  v-if="equipement.status === 'disponible' && equipement.stock_total > 0"
                  class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-0.5 text-xs font-semibold text-white"
                >
                  <CheckCircle2 :size="12" />
                  Disponible ({{ equipement.stock_total }} en stock)
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 rounded-full bg-rose-400/20 px-3 py-0.5 text-xs font-semibold text-white"
                >
                  <XCircle :size="12" />
                  {{ equipement.status === 'disponible' ? 'Rupture de stock' : 'Indisponible' }}
                </span>
                <span class="text-sm text-indigo-100">
                  ID #{{ equipement.id }}
                </span>
              </div>
            </div>
          </div>

          <!-- Grille d'informations -->
          <div class="grid grid-cols-1 gap-px bg-gray-100 sm:grid-cols-3">
            <!-- Stock Total -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                  <Boxes :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Stock Total</p>
                  <p class="mt-0.5 text-lg font-bold text-gray-900">{{ equipement.stock_total }} unités</p>
                </div>
              </div>
            </div>

            <!-- Réservations -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                  <Calendar :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Réservations associées</p>
                  <p class="mt-0.5 text-lg font-bold text-gray-900">
                    {{ equipement.reservations_count ?? 0 }} fois
                  </p>
                </div>
              </div>
            </div>

            <!-- Statut -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                  <CheckCircle2 :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Statut du catalogue</p>
                  <p class="mt-0.5 text-base font-bold capitalize text-gray-900">{{ equipement.status }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <Info :size="18" class="text-indigo-600" />
            <h3 class="text-base font-semibold text-gray-800">Description & Spécifications</h3>
          </div>
          <p class="text-sm leading-relaxed text-gray-600">
            {{ equipement.description || 'Aucune description fournie pour cet équipement.' }}
          </p>
        </div>

        <!-- DATES -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
          <h3 class="mb-4 text-base font-semibold text-gray-800">Historique</h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-gray-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Date d'enregistrement</p>
              <p class="mt-1 text-sm font-medium text-gray-700">{{ formatDate(equipement.created_at) }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Dernière modification</p>
              <p class="mt-1 text-sm font-medium text-gray-700">{{ formatDate(equipement.updated_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
