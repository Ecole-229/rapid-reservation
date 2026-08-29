<script setup>
import { RouterLink } from 'vue-router'
import { useAdminEquipementsStore } from '@/store/adminEquipements'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import EquipementsFilters from '@/components/admin/EquipementsFilters.vue'
import { ref, computed, onMounted } from 'vue'
import {
  Plus,
  Eye,
  Pencil,
  Trash2,
  AlertTriangle,
  Server,
  Calendar,
  RefreshCw,
  Boxes,
  X,
  Filter,
  PackageCheck,
  PackageX,
} from 'lucide-vue-next'

const adminEquipementsStore = useAdminEquipementsStore()

const search = ref('')
const status = ref('')
const minStock = ref(null)
const descending = ref(true)

// Labels lisibles des filtres actifs
const activeSearchLabel = ref('')
const activeStatusLabel = ref('')

// Modale de confirmation de suppression
const isDeleteModalOpen = ref(false)
const equipementToDelete = ref(null)
const isDeleting = ref(false)

const loadEquipements = async () => {
  try {
    const params = {
      all: 'true',
    }
    if (status.value) {
      params.status = status.value
    }
    if (search.value) {
      params.search = search.value
    }
    if (minStock.value !== null) {
      params.min_stock = minStock.value
    }
    await adminEquipementsStore.fetchEquipements(params)
  } catch (error) {
    console.error('Erreur lors du chargement des équipements:', error)
  }
}

onMounted(() => {
  loadEquipements()
})

const handleSearch = (value) => {
  search.value = value
  activeSearchLabel.value = value
  loadEquipements()
}

const handleStatusChange = (value) => {
  status.value = value
  const labels = { disponible: 'Disponible', indisponible: 'Indisponible' }
  activeStatusLabel.value = labels[value] || ''
  loadEquipements()
}

const handleStockChange = (value) => {
  minStock.value = value
  loadEquipements()
}

const handleSortChange = (value) => {
  descending.value = value
}

const resetFilters = () => {
  search.value = ''
  status.value = ''
  minStock.value = null
  activeSearchLabel.value = ''
  activeStatusLabel.value = ''
  loadEquipements()
}

const filteredEquipements = computed(() => {
  let result = [...adminEquipementsStore.equipements]

  if (minStock.value !== null) {
    result = result.filter((e) => Number(e.stock_total) >= minStock.value)
  }

  result.sort((a, b) => {
    return descending.value ? b.id - a.id : a.id - b.id
  })

  return result
})

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  try {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('fr-FR', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    }).format(date)
  } catch {
    return dateString
  }
}

const openDeleteModal = (equipement) => {
  equipementToDelete.value = equipement
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  equipementToDelete.value = null
}

const confirmDelete = async () => {
  if (!equipementToDelete.value) return
  isDeleting.value = true

  try {
    await adminEquipementsStore.deleteEquipement(equipementToDelete.value.id)
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
            Gestion des Équipements
          </h1>
          <p class="mt-1 text-sm text-[#64748B]">
            Consultez le matériel, les stocks disponibles, créez et gérez les équipements.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 active:scale-95"
            @click="loadEquipements"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': adminEquipementsStore.loading }" />
            <span>Actualiser</span>
          </button>

          <RouterLink
            :to="{ name: 'create-equipment' }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
          >
            <Plus :size="18" />
            <span>Ajouter un équipement</span>
          </RouterLink>
        </div>
      </div>

      <!-- MESSAGES FLASH -->
      <div
        v-if="adminEquipementsStore.successMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
      >
        <span>{{ adminEquipementsStore.successMessage }}</span>
        <button
          class="font-bold text-green-700 hover:text-green-900"
          @click="adminEquipementsStore.successMessage = null"
        >
          ×
        </button>
      </div>

      <div
        v-if="adminEquipementsStore.errorMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
      >
        <span>{{ adminEquipementsStore.errorMessage }}</span>
        <button
          class="font-bold text-red-700 hover:text-red-900"
          @click="adminEquipementsStore.errorMessage = null"
        >
          ×
        </button>
      </div>

      <!-- FILTRES -->
      <EquipementsFilters
        @search="handleSearch"
        @status-change="handleStatusChange"
        @stock-change="handleStockChange"
        @sort-change="handleSortChange"
      />

      <!-- BANDE RÉSUMÉ FILTRES -->
      <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <!-- Compteur résultats -->
        <div class="flex items-center gap-2">
          <Filter :size="15" class="text-[#64748B]" />
          <span class="text-sm font-medium text-[#64748B]">
            <span
              v-if="adminEquipementsStore.loading"
              class="text-[#94A3B8]"
            >Chargement...</span>
            <span v-else>
              <span class="font-bold text-[#0F172A]">{{ filteredEquipements.length }}</span>
              équipement{{ filteredEquipements.length > 1 ? 's' : '' }} trouvé{{ filteredEquipements.length > 1 ? 's' : '' }}
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
              @click="() => { search = ''; activeSearchLabel = ''; loadEquipements() }"
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
              @click="() => { status = ''; activeStatusLabel = ''; loadEquipements() }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Badge stock min -->
          <span
            v-if="minStock !== null"
            class="inline-flex items-center gap-1.5 rounded-full border border-purple-200 bg-purple-50 py-1 pl-3 pr-2 text-xs font-medium text-purple-700"
          >
            Stock ≥ {{ minStock }}
            <button
              type="button"
              class="flex h-4 w-4 items-center justify-center rounded-full bg-purple-200 text-purple-700 transition hover:bg-purple-300"
              @click="() => { minStock = null; loadEquipements() }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Réinitialiser -->
          <button
            v-if="activeSearchLabel || activeStatusLabel || minStock !== null"
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
        <div v-if="adminEquipementsStore.loading" class="flex flex-col items-center justify-center py-20">
          <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
          <p class="mt-4 text-sm font-medium text-gray-500">Chargement des équipements...</p>
        </div>

        <!-- LISTE VIDE -->
        <div
          v-else-if="filteredEquipements.length === 0"
          class="flex flex-col items-center justify-center py-16 text-center"
        >
          <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <Server :size="24" />
          </div>
          <h3 class="mt-4 text-base font-semibold text-gray-900">Aucun équipement trouvé</h3>
          <p class="mt-1 text-sm text-gray-500">
            Essayez de modifier vos critères de recherche ou enregistrez un nouvel équipement.
          </p>
        </div>

        <!-- TABLEAU -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Équipement
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Stock Total
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Statut
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Réservations
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Date d'ajout
                </th>
                <th class="px-6 py-4 text-right text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody class="divide-y divide-[#E2E8F0]">
              <tr
                v-for="equipement in filteredEquipements"
                :key="equipement.id"
                class="transition-colors duration-200 hover:bg-[#F8FAFC]"
              >
                <!-- NOM & APERÇU / IMAGE -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      v-if="equipement.image_url"
                      class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-gray-100 bg-gray-50"
                    >
                      <img
                        :src="equipement.image_url"
                        :alt="equipement.nom"
                        class="h-full w-full object-cover"
                      />
                    </div>
                    <div
                      v-else
                      class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-indigo-50 font-semibold text-indigo-600"
                    >
                      <Server :size="20" />
                    </div>
                    <div>
                      <p class="text-[14px] font-semibold text-[#0F172A]">
                        {{ equipement.nom }}
                      </p>
                      <p class="line-clamp-1 max-w-[280px] text-[12px] text-gray-400">
                        {{ equipement.description || 'Sans description' }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- STOCK TOTAL -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2 text-[14px] font-semibold">
                    <Boxes :size="16" class="text-indigo-500" />
                    <span :class="equipement.stock_total > 0 ? 'text-[#0F172A]' : 'text-rose-600'">
                      {{ equipement.stock_total }} unités
                    </span>
                  </div>
                </td>

                <!-- STATUT -->
                <td class="px-6 py-4">
                  <span
                    v-if="equipement.status === 'disponible' && equipement.stock_total > 0"
                    class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[12px] font-semibold text-emerald-700"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    Disponible
                  </span>
                  <span
                    v-else
                    class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-[12px] font-semibold text-rose-700"
                  >
                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                    {{ equipement.status === 'disponible' ? 'Rupture stock' : 'Indisponible' }}
                  </span>
                </td>

                <!-- RESERVATIONS -->
                <td class="px-6 py-4 text-[14px] text-[#64748B]">
                  <span class="inline-flex items-center rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                    {{ equipement.reservations_count ?? 0 }} fois réservé
                  </span>
                </td>

                <!-- DATE -->
                <td class="px-6 py-4 text-[14px] text-[#64748B]">
                  <div class="flex items-center gap-1.5">
                    <Calendar :size="14" class="text-gray-400" />
                    <span>{{ formatDate(equipement.created_at) }}</span>
                  </div>
                </td>

                <!-- ACTIONS -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <!-- Voir -->
                    <RouterLink
                      :to="{ name: 'info-equipment', params: { id: equipement.id } }"
                      title="Voir les détails"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
                    >
                      <Eye :size="15" />
                    </RouterLink>

                    <!-- Modifier -->
                    <RouterLink
                      :to="{ name: 'update-equipment', params: { id: equipement.id } }"
                      title="Modifier"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600"
                    >
                      <Pencil :size="15" />
                    </RouterLink>

                    <!-- Supprimer -->
                    <button
                      type="button"
                      title="Supprimer"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600"
                      @click="openDeleteModal(equipement)"
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
              Êtes-vous sûr de vouloir supprimer l'équipement
              <strong class="text-gray-800">{{ equipementToDelete?.nom }}</strong> ?
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
