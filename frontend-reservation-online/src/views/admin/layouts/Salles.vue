<script setup>
import { RouterLink } from 'vue-router'
import { useAdminSallesStore } from '@/store/adminSalles'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import SallesFilters from '@/components/admin/SallesFilters.vue'
import { ref, computed, onMounted } from 'vue'
import {
  Plus,
  Eye,
  Pencil,
  Trash2,
  AlertTriangle,
  DoorOpen,
  MapPin,
  Users as UsersIcon,
  Calendar,
  RefreshCw,
  Coins,
  X,
  Filter,
} from 'lucide-vue-next'

const adminSallesStore = useAdminSallesStore()

const search = ref('')
const status = ref('')
const descending = ref(true)

// Labels lisibles des filtres actifs
const activeSearchLabel = ref('')
const activeStatusLabel = ref('')

// Filtres avancés : prix et capacité
const priceFilter = ref({ min: null, max: null })
const capacityFilter = ref({ min: null, max: null })

// Modale de confirmation de suppression
const isDeleteModalOpen = ref(false)
const salleToDelete = ref(null)
const isDeleting = ref(false)

const loadSalles = async () => {
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
    await adminSallesStore.fetchSalles(params)
  } catch (error) {
    console.error('Erreur lors du chargement des salles:', error)
  }
}

onMounted(() => {
  loadSalles()
})

const handleSearch = (value) => {
  search.value = value
  activeSearchLabel.value = value
  loadSalles()
}

const handleStatusChange = (value) => {
  status.value = value
  const labels = { disponible: 'Disponible', indisponible: 'Indisponible' }
  activeStatusLabel.value = labels[value] || ''
  loadSalles()
}

const handleSortChange = (value) => {
  descending.value = value
}

const resetFilters = () => {
  search.value = ''
  status.value = ''
  activeSearchLabel.value = ''
  activeStatusLabel.value = ''
  priceFilter.value = { min: null, max: null }
  capacityFilter.value = { min: null, max: null }
  loadSalles()
}

const handlePriceChange = ({ min, max }) => {
  priceFilter.value = { min, max }
}

const handleCapacityChange = ({ min, max }) => {
  capacityFilter.value = { min, max }
}

const filteredSalles = computed(() => {
  let result = [...adminSallesStore.salles]

  // Filtre par prix
  if (priceFilter.value.min !== null) {
    result = result.filter((s) => Number(s.prix) >= priceFilter.value.min)
  }
  if (priceFilter.value.max !== null) {
    result = result.filter((s) => Number(s.prix) <= priceFilter.value.max)
  }

  // Filtre par capacité
  if (capacityFilter.value.min !== null) {
    result = result.filter((s) => Number(s.capacite) >= capacityFilter.value.min)
  }
  if (capacityFilter.value.max !== null) {
    result = result.filter((s) => Number(s.capacite) <= capacityFilter.value.max)
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

const formatPrice = (price) => {
  if (price === undefined || price === null) return '0 FCFA'
  return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA'
}

const openDeleteModal = (salle) => {
  salleToDelete.value = salle
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  salleToDelete.value = null
}

const confirmDelete = async () => {
  if (!salleToDelete.value) return
  isDeleting.value = true

  try {
    await adminSallesStore.deleteSalle(salleToDelete.value.id)
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
            Gestion des Salles
          </h1>
          <p class="mt-1 text-sm text-[#64748B]">
            Consultez, filtrez, créez et gérez l'ensemble des salles de réunion et d'événements.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <button
            type="button"
            class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 active:scale-95"
            @click="loadSalles"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': adminSallesStore.loading }" />
            <span>Actualiser</span>
          </button>

          <RouterLink
            :to="{ name: 'create-salle' }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
          >
            <Plus :size="18" />
            <span>Ajouter une salle</span>
          </RouterLink>
        </div>
      </div>

      <!-- MESSAGES FLASH -->
      <div
        v-if="adminSallesStore.successMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
      >
        <span>{{ adminSallesStore.successMessage }}</span>
        <button class="font-bold text-green-700 hover:text-green-900" @click="adminSallesStore.successMessage = null">
          ×
        </button>
      </div>

      <div
        v-if="adminSallesStore.errorMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
      >
        <span>{{ adminSallesStore.errorMessage }}</span>
        <button class="font-bold text-red-700 hover:text-red-900" @click="adminSallesStore.errorMessage = null">
          ×
        </button>
      </div>

      <!-- FILTRES -->
      <SallesFilters
        @search="handleSearch"
        @status-change="handleStatusChange"
        @sort-change="handleSortChange"
        @price-change="handlePriceChange"
        @capacity-change="handleCapacityChange"
      />

      <!-- BANDE RÉSUMÉ FILTRES -->
      <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <!-- Compteur résultats -->
        <div class="flex items-center gap-2">
          <Filter :size="15" class="text-[#64748B]" />
          <span class="text-sm font-medium text-[#64748B]">
            <span
              v-if="adminSallesStore.loading"
              class="text-[#94A3B8]"
            >Chargement...</span>
            <span v-else>
              <span class="font-bold text-[#0F172A]">{{ filteredSalles.length }}</span>
              salle{{ filteredSalles.length > 1 ? 's' : '' }} trouvée{{ filteredSalles.length > 1 ? 's' : '' }}
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
              @click="() => { search.value = ''; activeSearchLabel.value = ''; loadSalles() }"
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
              @click="() => { status.value = ''; activeStatusLabel.value = ''; loadSalles() }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Badge prix -->
          <span
            v-if="priceFilter.min !== null || priceFilter.max !== null"
            class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 py-1 pl-3 pr-2 text-xs font-medium text-emerald-700"
          >
            Tarif :
            <template v-if="priceFilter.min !== null && priceFilter.max !== null">
              {{ priceFilter.min.toLocaleString('fr-FR') }} – {{ priceFilter.max.toLocaleString('fr-FR') }} FCFA
            </template>
            <template v-else-if="priceFilter.min !== null">≥ {{ priceFilter.min.toLocaleString('fr-FR') }} FCFA</template>
            <template v-else>≤ {{ priceFilter.max.toLocaleString('fr-FR') }} FCFA</template>
            <button
              type="button"
              class="flex h-4 w-4 items-center justify-center rounded-full bg-emerald-200 text-emerald-700 transition hover:bg-emerald-300"
              @click="priceFilter.value = { min: null, max: null }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Badge capacité -->
          <span
            v-if="capacityFilter.min !== null || capacityFilter.max !== null"
            class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 py-1 pl-3 pr-2 text-xs font-medium text-amber-700"
          >
            Capacité :
            <template v-if="capacityFilter.min !== null && capacityFilter.max !== null">
              {{ capacityFilter.min }} – {{ capacityFilter.max }} places
            </template>
            <template v-else-if="capacityFilter.min !== null">≥ {{ capacityFilter.min }} places</template>
            <template v-else>≤ {{ capacityFilter.max }} places</template>
            <button
              type="button"
              class="flex h-4 w-4 items-center justify-center rounded-full bg-amber-200 text-amber-700 transition hover:bg-amber-300"
              @click="capacityFilter.value = { min: null, max: null }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Réinitialiser -->
          <button
            v-if="activeSearchLabel || activeStatusLabel || priceFilter.min !== null || priceFilter.max !== null || capacityFilter.min !== null || capacityFilter.max !== null"
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
        <div v-if="adminSallesStore.loading" class="flex flex-col items-center justify-center py-20">
          <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
          <p class="mt-4 text-sm font-medium text-gray-500">Chargement des salles...</p>
        </div>

        <!-- LISTE VIDE -->
        <div
          v-else-if="filteredSalles.length === 0"
          class="flex flex-col items-center justify-center py-16 text-center"
        >
          <div class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <DoorOpen :size="24" />
          </div>
          <h3 class="mt-4 text-base font-semibold text-gray-900">Aucune salle trouvée</h3>
          <p class="mt-1 text-sm text-gray-500">
            Essayez de modifier vos filtres de recherche ou ajoutez une nouvelle salle.
          </p>
        </div>

        <!-- TABLEAU -->
        <div v-else class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Salle
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Capacité & Lieu
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Tarif
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Statut
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
                v-for="salle in filteredSalles"
                :key="salle.id"
                class="transition-colors duration-200 hover:bg-[#F8FAFC]"
              >
                <!-- NOM & APERÇU -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-3">
                    <div
                      class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 font-semibold text-blue-700"
                    >
                      <DoorOpen :size="20" />
                    </div>
                    <div>
                      <p class="text-[14px] font-semibold text-[#0F172A]">
                        {{ salle.nom }}
                      </p>
                      <p class="line-clamp-1 text-[12px] text-gray-400">
                        {{ salle.description || 'Sans description' }}
                      </p>
                    </div>
                  </div>
                </td>

                <!-- CAPACITE & LOCALISATION -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1.5 text-[14px] text-[#0F172A]">
                    <UsersIcon :size="15" class="text-gray-400" />
                    <span>{{ salle.capacite }} places</span>
                  </div>
                  <div class="mt-0.5 flex items-center gap-1 text-[12px] text-gray-500">
                    <MapPin :size="12" class="text-gray-400" />
                    <span>{{ salle.localisation }}</span>
                  </div>
                </td>

                <!-- PRIX -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-1 text-[14px] font-medium text-emerald-700">
                    <Coins :size="15" class="text-emerald-500" />
                    <span>{{ formatPrice(salle.prix) }}</span>
                  </div>
                </td>

                <!-- STATUT -->
                <td class="px-6 py-4">
                  <span
                    v-if="salle.status === 'disponible'"
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
                    Indisponible
                  </span>
                </td>

                <!-- DATE -->
                <td class="px-6 py-4 text-[14px] text-[#64748B]">
                  <div class="flex items-center gap-1.5">
                    <Calendar :size="14" class="text-gray-400" />
                    <span>{{ formatDate(salle.created_at) }}</span>
                  </div>
                </td>

                <!-- ACTIONS -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <!-- Voir -->
                    <RouterLink
                      :to="{ name: 'info-salle', params: { id: salle.id } }"
                      title="Voir les détails"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
                    >
                      <Eye :size="15" />
                    </RouterLink>

                    <!-- Modifier -->
                    <RouterLink
                      :to="{ name: 'update-salle', params: { id: salle.id } }"
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
                      @click="openDeleteModal(salle)"
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
              Êtes-vous sûr de vouloir supprimer la salle
              <strong class="text-gray-800">{{ salleToDelete?.nom }}</strong> ?
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
