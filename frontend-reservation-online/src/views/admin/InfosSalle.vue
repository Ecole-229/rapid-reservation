<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminSallesStore } from '@/store/adminSalles'
import {
  ArrowLeft,
  Pencil,
  DoorOpen,
  MapPin,
  Users as UsersIcon,
  Coins,
  Calendar,
  Info,
  Loader2,
  CheckCircle2,
  XCircle,
} from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const adminSallesStore = useAdminSallesStore()

const salleId = route.params.id
const salle = ref(null)
const isFetching = ref(true)

onMounted(async () => {
  try {
    salle.value = await adminSallesStore.fetchSalle(salleId)
  } catch (error) {
    console.error('Erreur lors du chargement de la salle :', error)
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

const formatPrice = (price) => {
  if (price === undefined || price === null) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA'
}
</script>

<template>
  <AppAdmin>
    <div class="mx-auto max-w-5xl">
      <!-- EN-TÊTE & RETOUR -->
      <div class="mb-6">
        <RouterLink
          :to="{ name: 'admin-salles' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la liste des salles</span>
        </RouterLink>

        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">
              Fiche de la salle
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Informations complètes et détails de la salle sélectionnée.
            </p>
          </div>

          <RouterLink
            v-if="salle"
            :to="{ name: 'update-salle', params: { id: salleId } }"
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
        v-else-if="adminSallesStore.errorMessage && !salle"
        class="rounded-2xl border border-red-200 bg-red-50 p-8 text-center text-sm text-red-700"
      >
        <XCircle :size="32" class="mx-auto mb-3 text-red-400" />
        <p class="font-semibold">Salle introuvable</p>
        <p class="mt-1">{{ adminSallesStore.errorMessage }}</p>
      </div>

      <!-- CONTENU -->
      <div v-else-if="salle" class="space-y-6">
        <!-- CARTE PRINCIPALE -->
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
          <!-- Header coloré -->
          <div class="flex items-center gap-5 bg-gradient-to-r from-blue-600 to-indigo-600 p-6">
            <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-2xl bg-white/20">
              <DoorOpen :size="32" class="text-white" />
            </div>
            <div>
              <h2 class="text-xl font-bold text-white">
                {{ salle.nom }}
              </h2>
              <div class="mt-1 flex items-center gap-2">
                <span
                  v-if="salle.status === 'disponible'"
                  class="inline-flex items-center gap-1.5 rounded-full bg-emerald-400/20 px-3 py-0.5 text-xs font-semibold text-white"
                >
                  <CheckCircle2 :size="12" />
                  Disponible
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1.5 rounded-full bg-rose-400/20 px-3 py-0.5 text-xs font-semibold text-white"
                >
                  <XCircle :size="12" />
                  Indisponible
                </span>
                <span class="text-sm text-blue-100">
                  ID #{{ salle.id }}
                </span>
              </div>
            </div>
          </div>

          <!-- Grille d'informations -->
          <div class="grid grid-cols-1 gap-px bg-gray-100 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Capacité -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                  <UsersIcon :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Capacité</p>
                  <p class="mt-0.5 text-lg font-bold text-gray-900">{{ salle.capacite }} places</p>
                </div>
              </div>
            </div>

            <!-- Tarif -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                  <Coins :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Tarif</p>
                  <p class="mt-0.5 text-lg font-bold text-gray-900">{{ formatPrice(salle.prix) }}</p>
                </div>
              </div>
            </div>

            <!-- Localisation -->
            <div class="bg-white p-5">
              <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                  <MapPin :size="20" />
                </div>
                <div>
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Localisation</p>
                  <p class="mt-0.5 text-base font-semibold text-gray-900">{{ salle.localisation }}</p>
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
                  <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Réservations</p>
                  <p class="mt-0.5 text-lg font-bold text-gray-900">
                    {{ salle.reservations_count ?? 'N/A' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- DESCRIPTION -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
          <div class="mb-4 flex items-center gap-2">
            <Info :size="18" class="text-blue-600" />
            <h3 class="text-base font-semibold text-gray-800">Description</h3>
          </div>
          <p class="text-sm leading-relaxed text-gray-600">
            {{ salle.description || 'Aucune description fournie pour cette salle.' }}
          </p>
        </div>

        <!-- DATES -->
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
          <h3 class="mb-4 text-base font-semibold text-gray-800">Historique</h3>
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-gray-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Date de création</p>
              <p class="mt-1 text-sm font-medium text-gray-700">{{ formatDate(salle.created_at) }}</p>
            </div>
            <div class="rounded-xl bg-gray-50 p-4">
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Dernière modification</p>
              <p class="mt-1 text-sm font-medium text-gray-700">{{ formatDate(salle.updated_at) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
