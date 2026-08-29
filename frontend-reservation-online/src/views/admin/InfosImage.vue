<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import AppAdmin from '@/components/admin/AppAdmin.vue'
import { useAdminImagesStore } from '@/store/adminImages'
import {
  ArrowLeft,
  Pencil,
  Image as ImageIcon,
  DoorOpen,
  Calendar,
  Info,
  Loader2,
  ExternalLink,
  XCircle,
} from 'lucide-vue-next'

const route = useRoute()
const adminImagesStore = useAdminImagesStore()

const imageId = route.params.id
const image = ref(null)
const isFetching = ref(true)

onMounted(async () => {
  try {
    image.value = await adminImagesStore.fetchImage(imageId)
  } catch (error) {
    console.error('Erreur chargement image :', error)
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
          :to="{ name: 'admin-galeries' }"
          class="mb-3 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-gray-800"
        >
          <ArrowLeft :size="16" />
          <span>Retour à la galerie</span>
        </RouterLink>

        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">
              Détails du média
            </h1>
            <p class="mt-1 text-sm text-gray-500">
              Visualisation haute résolution et métadonnées de la photo.
            </p>
          </div>

          <RouterLink
            v-if="image"
            :to="{ name: 'update-image', params: { id: imageId } }"
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
        <p class="mt-3 text-sm text-gray-500">Chargement de la photo...</p>
      </div>

      <!-- ERREUR -->
      <div
        v-else-if="adminImagesStore.errorMessage && !image"
        class="rounded-2xl border border-red-200 bg-red-50 p-8 text-center text-sm text-red-700"
      >
        <XCircle :size="32" class="mx-auto mb-3 text-red-400" />
        <p class="font-semibold">Photo introuvable</p>
        <p class="mt-1">{{ adminImagesStore.errorMessage }}</p>
      </div>

      <!-- CONTENU -->
      <div v-else-if="image" class="space-y-6">
        <!-- GRANDE IMAGE -->
        <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
          <div class="relative max-h-[500px] w-full overflow-hidden bg-gray-900 flex items-center justify-center">
            <img
              v-if="image.url"
              :src="image.url"
              :alt="image.nom"
              class="max-h-[500px] w-full object-contain"
            />
            <div v-else class="flex h-64 items-center justify-center text-gray-400">
              <ImageIcon :size="48" />
            </div>

            <!-- BOUTON OUVRIR EN GRAND -->
            <a
              v-if="image.url"
              :href="image.url"
              target="_blank"
              rel="noopener noreferrer"
              class="absolute bottom-4 right-4 flex items-center gap-2 rounded-xl bg-black/60 px-4 py-2 text-xs font-semibold text-white backdrop-blur-md transition hover:bg-black/80"
            >
              <ExternalLink :size="14" />
              <span>Ouvrir en plein écran</span>
            </a>
          </div>

          <!-- DÉTAILS DE L'IMAGE -->
          <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div>
                <h2 class="text-xl font-bold text-gray-900">
                  {{ image.nom }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                  {{ image.designation || 'Aucune désignation spécifique fournie.' }}
                </p>
              </div>

              <!-- Salle associée -->
              <div class="flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50/60 px-4 py-2.5">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white">
                  <DoorOpen :size="18" />
                </div>
                <div>
                  <p class="text-xs font-medium text-gray-400">Salle associée</p>
                  <p class="text-sm font-bold text-gray-900">
                    {{ image.salle?.nom || 'Salle #' + image.salle_id }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MÉTADONNÉES TECHNIQUES & HISTORIQUE -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- FICHIER / SOURCE -->
          <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center gap-2">
              <Info :size="18" class="text-blue-600" />
              <h3 class="text-base font-semibold text-gray-800">Source du fichier</h3>
            </div>
            <p class="break-all rounded-xl bg-gray-50 p-3 font-mono text-xs text-gray-700">
              {{ image.path || 'Non spécifié' }}
            </p>
          </div>

          <!-- HISTORIQUE -->
          <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-base font-semibold text-gray-800">Historique</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div class="rounded-xl bg-gray-50 p-3">
                <p class="text-[11px] font-semibold uppercase text-gray-400">Date d'ajout</p>
                <p class="mt-1 text-xs font-medium text-gray-700">{{ formatDate(image.created_at) }}</p>
              </div>
              <div class="rounded-xl bg-gray-50 p-3">
                <p class="text-[11px] font-semibold uppercase text-gray-400">Dernière mise à jour</p>
                <p class="mt-1 text-xs font-medium text-gray-700">{{ formatDate(image.updated_at) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppAdmin>
</template>
