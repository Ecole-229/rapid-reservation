<script setup>
import { RouterLink } from 'vue-router'
import { useAdminReservationsStore } from '@/store/adminReservations'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import ReservationsFilters from '@/components/admin/ReservationsFilters.vue'
import { ref, computed, onMounted } from 'vue'
import {
  Plus,
  Eye,
  Pencil,
  Trash2,
  AlertTriangle,
  Calendar,
  Clock,
  DoorOpen,
  User,
  Users as UsersIcon,
  RefreshCw,
  X,
  Filter,
  CheckCircle2,
  XCircle,
  Check,
  Flag,
  Phone,
} from 'lucide-vue-next'

const adminReservationsStore = useAdminReservationsStore()

const search = ref('')
const status = ref('')
const selectedSalle = ref('')
const descending = ref(true)

// Labels lisibles des filtres actifs
const activeSearchLabel = ref('')
const activeStatusLabel = ref('')

// Modale de confirmation de suppression
const isDeleteModalOpen = ref(false)
const reservationToDelete = ref(null)
const isDeleting = ref(false)

const loadReservations = async () => {
  try {
    const params = {
      all: 'true',
    }
    if (status.value) {
      params.status = status.value
    }
    if (selectedSalle.value) {
      params.salle_id = selectedSalle.value
    }
    if (search.value) {
      params.search = search.value
    }
    await adminReservationsStore.fetchReservations(params)
  } catch (error) {
    console.error('Erreur lors du chargement des réservations:', error)
  }
}

onMounted(() => {
  loadReservations()
})

const handleSearch = (value) => {
  search.value = value
  activeSearchLabel.value = value
  loadReservations()
}

const handleStatusChange = (value) => {
  status.value = value
  const labels = {
    en_attente: 'En attente',
    confirmee: 'Confirmée',
    terminee: 'Terminée',
    rejetee: 'Rejetée',
  }
  activeStatusLabel.value = labels[value] || ''
  loadReservations()
}

const handleSalleChange = (value) => {
  selectedSalle.value = value
  loadReservations()
}

const handleSortChange = (value) => {
  descending.value = value
}

const resetFilters = () => {
  search.value = ''
  status.value = ''
  selectedSalle.value = ''
  activeSearchLabel.value = ''
  activeStatusLabel.value = ''
  loadReservations()
}

const filteredReservations = computed(() => {
  let result = [...adminReservationsStore.reservations]

  result.sort((a, b) => {
    return descending.value ? b.id - a.id : a.id - b.id
  })

  return result
})

const formatDateTime = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(date)
  } catch {
    return dateString
  }
}

const handleConfirm = async (reservation) => {
  try {
    await adminReservationsStore.confirmReservation(reservation.id)
  } catch (e) {
    console.error('Erreur confirmation réservation :', e)
  }
}

const handleReject = async (reservation) => {
  try {
    await adminReservationsStore.rejectReservation(reservation.id)
  } catch (e) {
    console.error('Erreur rejet réservation :', e)
  }
}

const handleTerminate = async (reservation) => {
  try {
    await adminReservationsStore.terminateReservation(reservation.id)
  } catch (e) {
    console.error('Erreur clôture réservation :', e)
  }
}

const openDeleteModal = (reservation) => {
  reservationToDelete.value = reservation
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  reservationToDelete.value = null
}

const confirmDelete = async () => {
  if (!reservationToDelete.value) return
  isDeleting.value = true

  try {
    await adminReservationsStore.deleteReservation(reservationToDelete.value.id)
    closeDeleteModal()
  } catch (error) {
    console.error('Erreur lors de la suppression :', error)
  } finally {
    isDeleting.value = false
  }
}
</script>

<template>
  <AppAdmin>
    <div class="min-h-screen bg-[#F8FAFC]">
      <!-- TITRE & ACTIONS -->
      <div class="mb-6 mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h1 class="text-[30px] font-bold tracking-[-0.8px] text-[#0F172A]">
            Gestion des Réservations
          </h1>
          <p class="mt-1 text-sm text-[#64748B]">
            Planifiez, confirmez, clôturez ou annulez les réservations de salles pour vos clients.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 active:scale-95"
            @click="loadReservations"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': adminReservationsStore.loading }" />
            <span>Actualiser</span>
          </button>

          <RouterLink
            :to="{ name: 'create-reservation' }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
          >
            <Plus :size="18" />
            <span>Nouvelle réservation</span>
          </RouterLink>
        </div>
      </div>

      <!-- MESSAGES FLASH -->
      <div
        v-if="adminReservationsStore.successMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
      >
        <span>{{ adminReservationsStore.successMessage }}</span>
        <button
          class="font-bold text-green-700 hover:text-green-900"
          @click="adminReservationsStore.successMessage = null"
        >
          ×
        </button>
      </div>

      <div
        v-if="adminReservationsStore.errorMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
      >
        <span>{{ adminReservationsStore.errorMessage }}</span>
        <button
          class="font-bold text-red-700 hover:text-red-900"
          @click="adminReservationsStore.errorMessage = null"
        >
          ×
        </button>
      </div>

      <!-- FILTRES -->
      <ReservationsFilters
        @search="handleSearch"
        @status-change="handleStatusChange"
        @salle-change="handleSalleChange"
        @sort-change="handleSortChange"
      />

      <!-- BANDE RÉSUMÉ FILTRES -->
      <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <!-- Compteur résultats -->
        <div class="flex items-center gap-2">
          <Filter :size="15" class="text-[#64748B]" />
          <span class="text-sm font-medium text-[#64748B]">
            <span
              v-if="adminReservationsStore.loading"
              class="text-[#94A3B8]"
            >Chargement...</span>
            <span v-else>
              <span class="font-bold text-[#0F172A]">{{ filteredReservations.length }}</span>
              réservation{{ filteredReservations.length > 1 ? 's' : '' }} trouvée{{ filteredReservations.length > 1 ? 's' : '' }}
            </span>
          </span>
        </div>

        <!-- Badges filtres actifs -->
        <div class="flex flex-wrap items-center gap-2">
          <!-- Badge recherche -->
          <span
            v-if="activeSearchLabel"
            class="inline-flex items-center gap-1.5 rounded-full border border-blue-200 bg-blue-50 py-1 pl-3 pr-2 text-xs font-medium text-blue-700"
          >
            Recherche : "{{ activeSearchLabel }}"
            <button
              type="button"
              class="flex h-4 w-4 items-center justify-center rounded-full bg-blue-200 text-blue-700 transition hover:bg-blue-300"
              @click="() => { search = ''; activeSearchLabel = ''; loadReservations() }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Badge statut -->
          <span
            v-if="activeStatusLabel"
            class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 py-1 pl-3 pr-2 text-xs font-medium text-indigo-700"
          >
            Statut : {{ activeStatusLabel }}
            <button
              type="button"
              class="flex h-4 w-4 items-center justify-center rounded-full bg-indigo-200 text-indigo-700 transition hover:bg-indigo-300"
              @click="() => { status = ''; activeStatusLabel = ''; loadReservations() }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Badge salle -->
          <span
            v-if="selectedSalle"
            class="inline-flex items-center gap-1.5 rounded-full border border-purple-200 bg-purple-50 py-1 pl-3 pr-2 text-xs font-medium text-purple-700"
          >
            Salle filtrée
            <button
              type="button"
              class="flex h-4 w-4 items-center justify-center rounded-full bg-purple-200 text-purple-700 transition hover:bg-purple-300"
              @click="() => { selectedSalle = ''; loadReservations() }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Réinitialiser -->
          <button
            v-if="activeSearchLabel || activeStatusLabel || selectedSalle"
            type="button"
            class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600 transition hover:bg-gray-50 hover:text-gray-900"
            @click="resetFilters"
          >
            <X :size="11" />
            Réinitialiser tout
          </button>
        </div>
      </div>

      <!-- TABLE / CONTENU -->
      <div
        class="mt-6 overflow-hidden rounded-[16px] border border-[#E2E8F0] bg-white shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
      >
        <!-- LOADING SPINNER -->
        <div v-if="adminReservationsStore.loading" class="flex flex-col items-center justify-center py-20">
          <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
          <p class="mt-4 text-sm font-medium text-gray-500">Chargement des réservations...</p>
        </div>

        <!-- LISTE VIDE -->
        <div
          v-else-if="filteredReservations.length === 0"
          class="flex flex-col items-center justify-center py-16 text-center"
        >
          <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <Calendar :size="24" />
          </div>
          <h3 class="mt-4 text-base font-semibold text-gray-900">Aucune réservation trouvée</h3>
          <p class="mt-1 text-sm text-gray-500">
            Créez une nouvelle réservation pour une personne ou modifiez vos critères de recherche.
          </p>
        </div>

        <!-- TABLEAU -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Client / Bénéficiaire
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Salle
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Créneau Horaire
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Invités
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Statut
                </th>
                <th class="px-6 py-4 text-right text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Actions & Gestion
                </th>
              </tr>
            </thead>

            <tbody class="divide-y divide-[#E2E8F0]">
              <tr
                v-for="res in filteredReservations"
                :key="res.id"
                class="transition-colors duration-200 hover:bg-[#F8FAFC]"
              >
                <!-- CLIENT -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="flex h-10 w-10 items-center justify-center rounded-xl font-semibold"
                      :class="res.user_id ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                    >
                      <User :size="18" />
                    </div>
                    <div>
                      <p class="text-[14px] font-semibold text-[#0F172A]">
                        {{ res.nom_affiche }}
                      </p>
                      <div class="flex items-center gap-1.5 text-[12px] text-gray-400">
                        <Phone :size="11" />
                        <span>{{ res.telephone_affiche || 'Non renseigné' }}</span>
                        <span
                          v-if="!res.user_id"
                          class="rounded bg-amber-100 px-1.5 py-0.2 text-[10px] font-medium text-amber-800"
                        >
                          Direct
                        </span>
                      </div>
                    </div>
                  </div>
                </td>

                <!-- SALLE -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2 text-[14px] font-medium text-[#0F172A]">
                    <DoorOpen :size="16" class="text-blue-500" />
                    <span>{{ res.salle?.nom || 'Salle #' + res.salle_id }}</span>
                  </div>
                  <p class="mt-0.5 text-[12px] text-gray-400">
                    {{ res.salle?.localisation }}
                  </p>
                </td>

                <!-- CRENEAU -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1.5 text-[13px] font-medium text-gray-800">
                    <Clock :size="14" class="text-gray-400" />
                    <span>{{ formatDateTime(res.date_heure_debut) }}</span>
                  </div>
                  <div class="mt-0.5 text-[12px] text-gray-500">
                    au {{ formatDateTime(res.date_heure_fin) }}
                  </div>
                </td>

                <!-- PERSONNES -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1.5 text-[13px] text-gray-700 font-medium">
                    <UsersIcon :size="14" class="text-gray-400" />
                    <span>{{ res.nombre_personnes }} pers.</span>
                  </div>
                  <span
                    v-if="res.equipements && res.equipements.length > 0"
                    class="mt-1 inline-block text-[11px] text-indigo-600 font-medium"
                  >
                    +{{ res.equipements.length }} équipement(s)
                  </span>
                </td>

                <!-- STATUT -->
                <td class="px-6 py-4">
                  <!-- En attente -->
                  <span
                    v-if="res.status === 'en_attente'"
                    class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[12px] font-semibold text-amber-700"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    En attente
                  </span>

                  <!-- Confirmée -->
                  <span
                    v-else-if="res.status === 'confirmee'"
                    class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[12px] font-semibold text-emerald-700"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Confirmée
                  </span>

                  <!-- Terminée -->
                  <span
                    v-else-if="res.status === 'terminee'"
                    class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-[12px] font-semibold text-slate-700"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span>
                    Terminée
                  </span>

                  <!-- Rejetée -->
                  <span
                    v-else
                    class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-[12px] font-semibold text-rose-700"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                    Rejetée
                  </span>
                </td>

                <!-- ACTIONS RAPIDES & CRUD -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <!-- Bouton Confirmer (si en attente) -->
                    <button
                      v-if="res.status === 'en_attente'"
                      type="button"
                      title="Confirmer la réservation"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-600 transition hover:bg-emerald-600 hover:text-white"
                      @click="handleConfirm(res)"
                    >
                      <Check :size="15" />
                    </button>

                    <!-- Bouton Terminer (si confirmée) -->
                    <button
                      v-if="res.status === 'confirmee'"
                      type="button"
                      title="Clôturer / Marquer comme terminée"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-600 transition hover:bg-slate-700 hover:text-white"
                      @click="handleTerminate(res)"
                    >
                      <Flag :size="14" />
                    </button>

                    <!-- Bouton Rejeter (si en attente ou confirmée) -->
                    <button
                      v-if="res.status === 'en_attente' || res.status === 'confirmee'"
                      type="button"
                      title="Rejeter / Annuler"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-600 hover:text-white"
                      @click="handleReject(res)"
                    >
                      <X :size="15" />
                    </button>

                    <!-- Voir détails -->
                    <RouterLink
                      :to="{ name: 'info-reservation', params: { id: res.id } }"
                      title="Voir le dossier complet"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
                    >
                      <Eye :size="15" />
                    </RouterLink>

                    <!-- Modifier -->
                    <RouterLink
                      :to="{ name: 'update-reservation', params: { id: res.id } }"
                      title="Modifier la réservation"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600"
                    >
                      <Pencil :size="15" />
                    </RouterLink>

                    <!-- Supprimer -->
                    <button
                      type="button"
                      title="Supprimer"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600"
                      @click="openDeleteModal(res)"
                    >
                      <Trash2 :size="15" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODALE DE CONFIRMATION DE SUPPRESSION -->
    <div
      v-if="isDeleteModalOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm"
    >
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-center gap-4">
          <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
            <AlertTriangle :size="24" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-gray-900">
              Confirmer la suppression
            </h3>
            <p class="mt-1 text-sm text-gray-500">
              Êtes-vous sûr de vouloir supprimer la réservation #{{ reservationToDelete?.id }} de
              <strong class="text-gray-800">{{ reservationToDelete?.nom_affiche }}</strong> ?
            </p>
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button
            type="button"
            class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            @click="closeDeleteModal"
          >
            Annuler
          </button>

          <button
            type="button"
            :disabled="isDeleting"
            class="flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:opacity-50"
            @click="confirmDelete"
          >
            <span v-if="isDeleting">Suppression...</span>
            <span v-else>Supprimer définitivement</span>
          </button>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
