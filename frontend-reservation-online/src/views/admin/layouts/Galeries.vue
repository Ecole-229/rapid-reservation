<script setup>
import { RouterLink } from 'vue-router'
import { useAdminImagesStore } from '@/store/adminImages'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import ImagesFilters from '@/components/admin/ImagesFilters.vue'
import { ref, computed, onMounted } from 'vue'
import {
  Plus,
  Eye,
  Pencil,
  Trash2,
  AlertTriangle,
  Image as ImageIcon,
  DoorOpen,
  Calendar,
  RefreshCw,
  X,
  Filter,
  Layers,
  LayoutGrid,
  List,
} from 'lucide-vue-next'

const adminImagesStore = useAdminImagesStore()

const search = ref('')
const selectedSalle = ref('')
const descending = ref(true)
const viewMode = ref('grid') // 'grid' ou 'table'

// Labels lisibles des filtres actifs
const activeSearchLabel = ref('')

// Modale de confirmation de suppression
const isDeleteModalOpen = ref(false)
const imageToDelete = ref(null)
const isDeleting = ref(false)

const loadImages = async () => {
  try {
    const params = {
      all: 'true',
    }
    if (selectedSalle.value) {
      params.salle_id = selectedSalle.value
    }
    if (search.value) {
      params.search = search.value
    }
    await adminImagesStore.fetchImages(params)
  } catch (error) {
    console.error('Erreur lors du chargement des images:', error)
  }
}

onMounted(() => {
  loadImages()
})

const handleSearch = (value) => {
  search.value = value
  activeSearchLabel.value = value
  loadImages()
}

const handleSalleChange = (value) => {
  selectedSalle.value = value
  loadImages()
}

const handleSortChange = (value) => {
  descending.value = value
}

const resetFilters = () => {
  search.value = ''
  selectedSalle.value = ''
  activeSearchLabel.value = ''
  loadImages()
}

const filteredImages = computed(() => {
  let result = [...adminImagesStore.images]

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

const openDeleteModal = (img) => {
  imageToDelete.value = img
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  imageToDelete.value = null
}

const confirmDelete = async () => {
  if (!imageToDelete.value) return
  isDeleting.value = true

  try {
    await adminImagesStore.deleteImage(imageToDelete.value.id)
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
            Galerie & Médias des Salles
          </h1>
          <p class="mt-1 text-sm text-[#64748B]">
            Gérez les photos, perspectives et visuels associés aux différentes salles.
          </p>
        </div>

        <div class="flex items-center gap-3">
          <!-- Bascule Grid / Table -->
          <div class="flex items-center rounded-xl border border-gray-200 bg-white p-1 shadow-sm">
            <button
              type="button"
              class="flex h-8 w-8 items-center justify-center rounded-lg transition"
              :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-800'"
              title="Vue Grille"
              @click="viewMode = 'grid'"
            >
              <LayoutGrid :size="16" />
            </button>
            <button
              type="button"
              class="flex h-8 w-8 items-center justify-center rounded-lg transition"
              :class="viewMode === 'table' ? 'bg-blue-600 text-white' : 'text-gray-500 hover:text-gray-800'"
              title="Vue Tableau"
              @click="viewMode = 'table'"
            >
              <List :size="16" />
            </button>
          </div>

          <button
            type="button"
            class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 active:scale-95"
            @click="loadImages"
          >
            <RefreshCw :size="16" :class="{ 'animate-spin': adminImagesStore.loading }" />
            <span>Actualiser</span>
          </button>

          <RouterLink
            :to="{ name: 'create-image' }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 active:scale-95"
          >
            <Plus :size="18" />
            <span>Ajouter une image</span>
          </RouterLink>
        </div>
      </div>

      <!-- MESSAGES FLASH -->
      <div
        v-if="adminImagesStore.successMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800"
      >
        <span>{{ adminImagesStore.successMessage }}</span>
        <button
          class="font-bold text-green-700 hover:text-green-900"
          @click="adminImagesStore.successMessage = null"
        >
          ×
        </button>
      </div>

      <div
        v-if="adminImagesStore.errorMessage"
        class="mb-6 flex items-center justify-between rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"
      >
        <span>{{ adminImagesStore.errorMessage }}</span>
        <button
          class="font-bold text-red-700 hover:text-red-900"
          @click="adminImagesStore.errorMessage = null"
        >
          ×
        </button>
      </div>

      <!-- FILTRES -->
      <ImagesFilters
        @search="handleSearch"
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
              v-if="adminImagesStore.loading"
              class="text-[#94A3B8]"
            >Chargement...</span>
            <span v-else>
              <span class="font-bold text-[#0F172A]">{{ filteredImages.length }}</span>
              photo{{ filteredImages.length > 1 ? 's' : '' }} trouvée{{ filteredImages.length > 1 ? 's' : '' }}
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
              @click="() => { search = ''; activeSearchLabel = ''; loadImages() }"
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
              @click="() => { selectedSalle = ''; loadImages() }"
            >
              <X :size="10" />
            </button>
          </span>

          <!-- Réinitialiser -->
          <button
            v-if="activeSearchLabel || selectedSalle"
            type="button"
            class="inline-flex items-center gap-1 rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600 transition hover:bg-gray-50 hover:text-gray-900"
            @click="resetFilters"
          >
            <X :size="11" />
            Réinitialiser
          </button>
        </div>
      </div>

      <!-- LOADING SPINNER -->
      <div v-if="adminImagesStore.loading" class="flex flex-col items-center justify-center py-24">
        <div class="h-10 w-10 animate-spin rounded-full border-4 border-blue-600 border-t-transparent"></div>
        <p class="mt-4 text-sm font-medium text-gray-500">Chargement de la galerie...</p>
      </div>

      <!-- LISTE VIDE -->
      <div
        v-else-if="filteredImages.length === 0"
        class="mt-6 flex flex-col items-center justify-center rounded-2xl border border-gray-200 bg-white py-20 text-center shadow-sm"
      >
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-500">
          <ImageIcon :size="32" />
        </div>
        <h3 class="mt-4 text-lg font-semibold text-gray-900">Aucune photo dans la galerie</h3>
        <p class="mt-1 text-sm text-gray-500">
          Ajoutez de superbes visuels pour valoriser vos salles de réunion.
        </p>
        <RouterLink
          :to="{ name: 'create-image' }"
          class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
        >
          <Plus :size="18" />
          <span>Ajouter la première image</span>
        </RouterLink>
      </div>

      <!-- AFFICHAGE EN GRILLE -->
      <div
        v-else-if="viewMode === 'grid'"
        class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
      >
        <div
          v-for="img in filteredImages"
          :key="img.id"
          class="group relative flex flex-col overflow-hidden rounded-2xl border border-[#E2E8F0] bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md"
        >
          <!-- IMAGE APERÇU -->
          <div class="relative h-48 w-full overflow-hidden bg-gray-100">
            <img
              v-if="img.url"
              :src="img.url"
              :alt="img.nom"
              class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
            />
            <div
              v-else
              class="flex h-full w-full items-center justify-center bg-gray-100 text-gray-400"
            >
              <ImageIcon :size="36" />
            </div>

            <!-- BADGE SALLE -->
            <div class="absolute left-3 top-3">
              <span class="inline-flex items-center gap-1.5 rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white backdrop-blur-md">
                <DoorOpen :size="12" />
                {{ img.salle?.nom || 'Salle #' + img.salle_id }}
              </span>
            </div>
          </div>

          <!-- CONTENU CARTE -->
          <div class="flex flex-1 flex-col justify-between p-4">
            <div>
              <h3 class="font-semibold text-[#0F172A] truncate">
                {{ img.nom }}
              </h3>
              <p class="mt-1 text-xs text-gray-500 line-clamp-1">
                {{ img.designation || 'Sans désignation particulière' }}
              </p>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-3 text-xs text-gray-400">
              <span>{{ formatDate(img.created_at) }}</span>

              <!-- ACTIONS -->
              <div class="flex items-center gap-1">
                <RouterLink
                  :to="{ name: 'info-image', params: { id: img.id } }"
                  title="Voir les détails"
                  class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
                >
                  <Eye :size="13" />
                </RouterLink>

                <RouterLink
                  :to="{ name: 'update-image', params: { id: img.id } }"
                  title="Modifier"
                  class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600"
                >
                  <Pencil :size="13" />
                </RouterLink>

                <button
                  type="button"
                  title="Supprimer"
                  class="flex h-7 w-7 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600"
                  @click="openDeleteModal(img)"
                >
                  <Trash2 :size="13" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- AFFICHAGE EN TABLEAU -->
      <div
        v-else
        class="mt-6 overflow-hidden rounded-[16px] border border-[#E2E8F0] bg-white shadow-[0_4px_20px_-4px_rgba(15,23,42,0.06)]"
      >
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Image
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Nom & Désignation
                </th>
                <th class="px-6 py-4 text-[12px] font-semibold uppercase tracking-wide text-[#64748B]">
                  Salle associée
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
                v-for="img in filteredImages"
                :key="img.id"
                class="transition-colors duration-200 hover:bg-[#F8FAFC]"
              >
                <!-- APERÇU -->
                <td class="px-6 py-4">
                  <div class="h-14 w-20 flex-shrink-0 overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
                    <img
                      v-if="img.url"
                      :src="img.url"
                      :alt="img.nom"
                      class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center text-gray-400">
                      <ImageIcon :size="20" />
                    </div>
                  </div>
                </td>

                <!-- NOM & DESIGNATION -->
                <td class="px-6 py-4">
                  <p class="text-[14px] font-semibold text-[#0F172A]">
                    {{ img.nom }}
                  </p>
                  <p class="text-[12px] text-gray-400">
                    {{ img.designation || 'Sans désignation' }}
                  </p>
                </td>

                <!-- SALLE -->
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2 text-[14px] font-medium text-gray-700">
                    <DoorOpen :size="16" class="text-blue-500" />
                    <span>{{ img.salle?.nom || 'Salle #' + img.salle_id }}</span>
                  </div>
                </td>

                <!-- DATE -->
                <td class="px-6 py-4 text-[14px] text-[#64748B]">
                  <div class="flex items-center gap-1.5">
                    <Calendar :size="14" class="text-gray-400" />
                    <span>{{ formatDate(img.created_at) }}</span>
                  </div>
                </td>

                <!-- ACTIONS -->
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <RouterLink
                      :to="{ name: 'info-image', params: { id: img.id } }"
                      title="Voir les détails"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
                    >
                      <Eye :size="15" />
                    </RouterLink>

                    <RouterLink
                      :to="{ name: 'update-image', params: { id: img.id } }"
                      title="Modifier"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600"
                    >
                      <Pencil :size="15" />
                    </RouterLink>

                    <button
                      type="button"
                      title="Supprimer"
                      class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-red-300 hover:bg-red-50 hover:text-red-600"
                      @click="openDeleteModal(img)"
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
              Êtes-vous sûr de vouloir supprimer l'image
              <strong class="text-gray-800">{{ imageToDelete?.nom }}</strong> ?
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
